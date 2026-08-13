<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\products\FeeSpecification;

use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

/**
 * Hydrate an array definition with the FeeSpecification class, down to the rate
 * it derives from.
 *
 * A fee is read in two places at once — `price` to compute an amount, `rate` to
 * explain it — and the two carry different units on purpose. Typing the head
 * alone would leave a consumer reading `->price` on the fee and
 * `['price']` on the rate beside it, on a pair whose whole point is that both
 * are read together.
 *
 * The issuing body is typed too, through {@see hydrateOrganizationOrPerson()},
 * which reads the payload's `@type` rather than guessing from the declared
 * union.
 *
 * ⚠️ Only the constructor path needs this. `Reflection::hydrate()` types both on
 * its own, {@see FeeSpecification::$rate} declaring the attribute for it.
 *
 * Use it in the 'products' definition in the DI container.
 *
 * @throws HydrationException
 * @throws ReflectionException
 * @since 1.4.0
 */
function hydrateFeeSpecification( mixed $init = null ) :?FeeSpecification
{
    $fee = null ;

    if( $init instanceof FeeSpecification )
    {
        $fee = $init ;
    }
    else if( is_array( $init ) )
    {
        $fee = new FeeSpecification( $init ) ;
    }

    if( $fee instanceof FeeSpecification )
    {
        // A rate already typed is left alone : re-wrapping it would rebuild what
        // a caller has, possibly, already enriched.
        $rate = $fee->rate ?? null ;
        if( !( $rate instanceof UnitPriceSpecification ) && is_array( $rate ) )
        {
            $fee->rate = new UnitPriceSpecification( $rate ) ;
        }

        $publisher = $fee->publisher ?? null ;
        if( is_array( $publisher ) )
        {
            $fee->publisher = hydrateOrganizationOrPerson( $publisher ) ;
        }
    }

    return $fee instanceof FeeSpecification ? $fee : null ;
}