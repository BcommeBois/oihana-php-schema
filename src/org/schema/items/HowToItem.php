<?php

namespace org\schema\items;

use org\schema\ListItem;
use org\schema\QuantitativeValue;

/**
 * An list item, e.g. a step in a checklist or how-to description.
 * @see https://schema.org/HowToItem
 */
class HowToItem extends ListItem
{
    /**
     * The required quantity of the item(s).
     * @var float|int|QuantitativeValue|string|null
     */
    public null|float|int|QuantitativeValue|string $requiredQuantity ;
}