<?php

namespace xyz\oihana\schema\helpers\hydrate;

use xyz\oihana\schema\products\ApplicableResource;
use xyz\oihana\schema\products\Product;

/**
 * Hydrate an array definition with the ApplicableResource class, down to the
 * resource it points at.
 *
 * A link is read for two things at once — the flag, to know whether it applies,
 * and the resource, to know what applies — so typing the head alone would leave
 * a consumer reading `->appliedByDefault` on the link and `['id']` on the
 * resource beside it.
 *
 * ⚠️ **Only the constructor path needs this.** `Reflection::hydrate()` types the
 * entries on its own, {@see \xyz\oihana\schema\products\Product::$hasApplicableResource}
 * declaring the attribute for it. The two doors have to agree, which is what
 * this function is for.
 *
 * 🔑 **`item` is typed as a {@see Product}**, because that is what an applicable
 * resource is today : a record of the catalogue like any other. The day the
 * payload's own `@type` has to decide — a `Service` beside a `Product` — this
 * becomes a `hydrateProductOrService()` on the model of
 * {@see \org\schema\helpers\hydrate\hydrateOrganizationOrPerson()}. Guessing
 * from a declared union instead is exactly the mistake that pattern exists to
 * prevent.
 *
 * Use it in the 'products' definition in the DI container.
 *
 * @param mixed $init The definition to hydrate.
 *
 * @return ApplicableResource|null The link, or null when there is nothing to build.
 *
 * @since 1.4.0
 */
function hydrateApplicableResource( mixed $init = null ) :?ApplicableResource
{
    $link = null ;

    if( $init instanceof ApplicableResource )
    {
        $link = $init ;
    }
    else if( is_array( $init ) )
    {
        $link = new ApplicableResource( $init ) ;
    }

    if( $link instanceof ApplicableResource )
    {
        // A resource already typed is left alone : re-wrapping it would rebuild
        // what a caller has, possibly, already enriched.
        $item = $link->item ?? null ;

        if( is_array( $item ) )
        {
            $link->item = new Product( $item ) ;
        }
    }

    return $link instanceof ApplicableResource ? $link : null ;
}
