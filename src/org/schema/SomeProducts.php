<?php

namespace org\schema;

use oihana\reflect\attributes\HydrateWith;

/**
 * A placeholder for multiple similar products of the same kind.
 *
 * @see https://schema.org/SomeProducts
 */
class SomeProducts extends Product
{
    /**
     * The current approximate inventory level for the item or items.
     * @var array|QuantitativeValue|null
     */
    #[HydrateWith(QuantitativeValue::class)]
    public null|array|QuantitativeValue $inventoryLevel ;
}