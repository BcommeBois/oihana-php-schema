<?php

namespace org\schema;

use org\schema\traits\ItemListTrait;

/**
 * A list of items of any sort—for example, Top 10 Movies About Weathermen, or Top 100 Party Songs. Not to be confused with HTML lists, which are often used only for formatting.
 * @see https://schema.org/ItemList
 */
class ItemList extends Intangible
{
    use ItemListTrait ;
}