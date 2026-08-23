<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\Offer;
use org\schema\Product;
use org\schema\Service;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the bare {@see Offer} class.
 *
 * The two offer helpers this package already carries answer narrower questions :
 * {@see hydrateOfferPurchase()} only ever builds an `OfferForPurchase`, and
 * {@see \xyz\oihana\schema\helpers\hydrate\hydrateAggregateOffer()} an aggregate. A plain
 * `Offer` — what {@see \org\schema\Organization::$makesOffer} carries, and with it every
 * property that lists bare offers — had none.
 *
 * Handles both a single offer array and an array of offers. The offer itself is built
 * through {@see Reflection::hydrate()}, which honors the `#[HydrateAs]` attributes the
 * class declares — `eligibleQuantity` comes out a `QuantitativeValue`, `priceSpecification`
 * a `PriceSpecification`, its union naming a single class that reflection resolves on its
 * own.
 *
 * `itemOffered` is the exception : its `CreativeWork|Event|Product|Service` union cannot be
 * resolved from the property type alone, so the target class is read from the payload's
 * JSON-LD `@type`, exactly as {@see \xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLineItem()}
 * does for a document line :
 *
 * - a `@type` ending with `Service` (e.g. `Service`, `FoodService`) gives a {@see Service} ;
 * - anything else gives `$productClass`.
 *
 * 🔑 **`$productClass` is what keeps this helper in `org\schema`.** The commerce-enriched
 * product of this package lives in `xyz\oihana\schema\products` and `org` knows nothing of
 * `xyz` — so the default is the plain Schema.org {@see Product}, and a caller on the
 * business side passes its own, as long as it stays a subclass so the declared union still
 * holds.
 *
 * Anything that is not an array — an unresolved string reference, an already typed
 * instance — is left untouched.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed  $init         Single offer data, a list of offer data, or any other value.
 * @param string $productClass The class hydrated when the item offered is not a service. Must extend {@see Product}.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $offers = hydrateOffer
 * ([
 *     [
 *         'itemOffered' => [ '@type' => 'Product' , 'name' => 'Model A widget' ] ,
 *         'description' => 'Worth showing at the next meeting.' ,
 *     ] ,
 * ]) ;
 *
 * $offers[ 0 ]->itemOffered instanceof Product ; // true
 * ```
 */
function hydrateOffer( mixed $init = null , string $productClass = Product::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $offers = array_map
        (
            fn( $offer ) => hydrateOffer( $offer , $productClass ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $offers , fn( $offer ) => $offer instanceof Offer || is_scalar( $offer ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so a
    // list of offers costs one plan, not one plan per offer.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $offer = $reflection->hydrate( $init , Offer::class ) ;

    // ------- itemOffered
    // Read from the raw payload : the union resolution done by hydrate() ignores the `@type`.

    $itemOffered = $init[ Schema::ITEM_OFFERED ] ?? null ;
    if( is_array( $itemOffered ) )
    {
        $resolve = static function( array $item ) use ( $reflection , $productClass ) :object
        {
            $type = $item[ Schema::AT_TYPE ] ?? null ;

            $class = is_string( $type ) && str_ends_with( strtolower( $type ) , 'service' )
                   ? Service::class
                   : $productClass ;

            return $reflection->hydrate( $item , $class ) ;
        } ;

        if( isIndexed( $itemOffered ) )
        {
            $items = array_map
            (
                fn( $item ) => is_array( $item ) ? $resolve( $item ) : $item ,
                $itemOffered
            );

            // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
            // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
            $filtered = array_values( array_filter( $items , fn( $item ) => $item instanceof Product || $item instanceof Service || is_scalar( $item ) ) ) ;

            $offer->itemOffered = count( $filtered ) > 0 ? $filtered : null ;
        }
        else
        {
            $offer->itemOffered = $resolve( $itemOffered ) ;
        }
    }

    return $offer ;
}
