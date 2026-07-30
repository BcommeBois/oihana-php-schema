<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

/**
 * Hydrate an array definition with the BusinessDocument class, or one of its subclasses
 * ({@see \xyz\oihana\schema\business\documents\Quote}, {@see \xyz\oihana\schema\business\documents\PurchaseOrder},
 * {@see Invoice}, {@see \xyz\oihana\schema\business\documents\CreditNote},
 * {@see \xyz\oihana\schema\business\documents\DebitNote}, {@see \xyz\oihana\schema\business\documents\DeliveryNote},
 * {@see \xyz\oihana\schema\business\documents\GoodsReceiptConfirmation}, {@see \xyz\oihana\schema\business\documents\Receipt},
 * {@see \xyz\oihana\schema\business\documents\RemittanceAdvice}, {@see \xyz\oihana\schema\business\documents\Statement}).
 *
 * Handles both a single document array and an array of documents. The document itself is
 * built through {@see Reflection::hydrate()}, which honors every `#[HydrateAs]`/`#[HydrateWith]`
 * attribute declared across the hierarchy (`adjustments`, `taxes`, `totals`, `documentLines`...).
 *
 * `customer`, `seller` and `author` are the exception, on every document class : their
 * `Organization|Person` union cannot be resolved from the property type alone — reflection
 * always picks `Organization`, even for a `Person` payload — so they are re-resolved from
 * the raw payload through {@see hydrateOrganizationOrPerson()}. On an {@see Invoice}, `broker`
 * and `provider` carry the same union and get the same treatment.
 *
 * @param mixed  $init  Single document data or array of document data.
 * @param string $class The BusinessDocument subclass to hydrate into. Defaults to
 *                       {@see BusinessDocument} itself.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * hydrateBusinessDocument( $raw ) ;                          // BusinessDocument
 * hydrateBusinessDocument( $raw , Invoice::class ) ;          // Invoice — also resolves broker/provider
 * hydrateBusinessDocument( $raw , Quote::class ) ;            // Quote
 * ```
 */
function hydrateBusinessDocument( mixed $init = null , string $class = BusinessDocument::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $documents = array_map
        (
            fn( $document ) => hydrateBusinessDocument( $document , $class ) ,
            $init
        );

        $filtered = array_values( array_filter( $documents , fn( $document ) => $document instanceof BusinessDocument ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $document = $reflection->hydrate( $init , $class ) ;

    foreach( [ BusinessDocument::CUSTOMER , BusinessDocument::SELLER , BusinessDocument::AUTHOR ] as $property )
    {
        $resolved = hydrateOrganizationOrPerson( $init[ $property ] ?? null ) ;
        if( $resolved !== null )
        {
            $document->{ $property } = $resolved ;
        }
    }

    if( $document instanceof Invoice )
    {
        foreach( [ Invoice::BROKER , Invoice::PROVIDER ] as $property )
        {
            $resolved = hydrateOrganizationOrPerson( $init[ $property ] ?? null ) ;
            if( $resolved !== null )
            {
                $document->{ $property } = $resolved ;
            }
        }
    }

    return $document ;
}
