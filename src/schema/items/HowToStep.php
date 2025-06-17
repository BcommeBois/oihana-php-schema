<?php

namespace org\schema\items;

use org\schema\ListItem;
use org\schema\traits\CreativeWorkTrait;
use org\schema\traits\ItemListTrait;

/**
 * A step in the instructions for how to achieve a result. It is an ordered list with HowToDirection and/or HowToTip items.
 * @see https://schema.org/HowToStep
 */
class HowToStep extends ListItem
{
    use CreativeWorkTrait ,
        ItemListTrait ;
}