<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\DefinedTerm;
use org\schema\ParcelDelivery;

use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;
use function org\schema\helpers\hydrate\hydratePostalAddress;

/**
 * Hydrate an array definition with the ParcelDelivery class — where the goods go,
 * how they travel, on which round and for when.
 *
 * Handles both a single delivery array and an array of them.
 *
 * The delivery is built through the {@see ParcelDelivery} constructor, and its
 * references are resolved one by one afterwards. {@see \oihana\reflect\Reflection::hydrate()}
 * would bring nothing here : the class declares no `#[HydrateAs]` on
 * `deliveryAddress`, `hasDeliveryMethod` or `hasDeliveryRoute`, so reflection
 * would leave the three of them raw while adding its own strictness — it drops
 * whatever the class does not declare, and a delivery read back from a store
 * commonly carries more than Schema.org names.
 *
 * The two travel terms are **parameters rather than hard-wired classes**, and
 * that is the reason this helper lives under `xyz\oihana` while
 * {@see ParcelDelivery} is plain `org\schema` : a thesaurus term belongs to the
 * business layer, and an attribute on the Schema.org class would make the lower
 * layer depend on the upper one. The defaults name the business terms; any
 * {@see DefinedTerm} subclass may replace them, which is what the property's own
 * declared type asks for.
 *
 * `provider` — the carrier — carries the `Organization|Person` union that
 * reflection cannot resolve from the property type alone, so it goes through
 * {@see hydrateOrganizationOrPerson()}, which reads the payload's `@type`.
 *
 * Each resolution happens only when the value is still an array — when there is
 * something to hydrate — and its answer is then written as is, `null` included :
 * an array that resolves to nothing becomes `null`, never a leftover raw array.
 *
 * @param mixed                     $init                Single delivery data or array of delivery data.
 * @param class-string<DefinedTerm> $deliveryMethodClass The class the delivery method is hydrated into.
 * @param class-string<DefinedTerm> $deliveryRouteClass  The class the delivery route is hydrated into.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $delivery = hydrateParcelDelivery
 * ([
 *     'deliveryAddress'   => [ 'streetAddress' => '8 Rue Paul Gros' , 'postalCode' => '33270' ] ,
 *     'hasDeliveryMethod' => [ 'id' => 'F13' , 'name' => 'Franco de port' ] ,
 *     'hasDeliveryRoute'  => [ 'id' => '54'  , 'name' => 'Libourne / rive droite' ] ,
 * ]) ;
 *
 * $delivery->deliveryAddress   instanceof PostalAddress      ; // true
 * $delivery->hasDeliveryMethod instanceof DeliveryMethodTerm ; // true
 * $delivery->hasDeliveryRoute  instanceof DeliveryRouteTerm  ; // true
 * ```
 */
function hydrateParcelDelivery
(
    mixed  $init                = null ,
    string $deliveryMethodClass = DeliveryMethodTerm::class ,
    string $deliveryRouteClass  = DeliveryRouteTerm::class
)
:mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $deliveries = array_map
        (
            fn( $delivery ) => hydrateParcelDelivery( $delivery , $deliveryMethodClass , $deliveryRouteClass ) ,
            $init
        );

        $filtered = array_values( array_filter( $deliveries , fn( $delivery ) => $delivery instanceof ParcelDelivery ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $delivery = new ParcelDelivery( $init ) ;

    // ------- deliveryAddress

    $deliveryAddress = $delivery->deliveryAddress ?? null ;
    if( is_array( $deliveryAddress ) )
    {
        $delivery->deliveryAddress = hydratePostalAddress( $deliveryAddress ) ;
    }

    // ------- originAddress

    $originAddress = $delivery->originAddress ?? null ;
    if( is_array( $originAddress ) )
    {
        $delivery->originAddress = hydratePostalAddress( $originAddress ) ;
    }

    // ------- hasDeliveryMethod

    $hasDeliveryMethod = $delivery->hasDeliveryMethod ?? null ;
    if( is_array( $hasDeliveryMethod ) )
    {
        $delivery->hasDeliveryMethod = hydrateDefinedTerm( $hasDeliveryMethod , $deliveryMethodClass ) ;
    }

    // ------- hasDeliveryRoute

    $hasDeliveryRoute = $delivery->hasDeliveryRoute ?? null ;
    if( is_array( $hasDeliveryRoute ) )
    {
        $delivery->hasDeliveryRoute = hydrateDefinedTerm( $hasDeliveryRoute , $deliveryRouteClass ) ;
    }

    // ------- provider

    $provider = $delivery->provider ?? null ;
    if( is_array( $provider ) )
    {
        $delivery->provider = hydrateOrganizationOrPerson( $provider ) ;
    }

    return $delivery ;
}
