<?php

namespace org\schema\items;

use org\schema\ListItem;
use org\schema\traits\CreativeWorkTrait;
use org\schema\traits\ItemListTrait;

/**
 * A sub-grouping of steps in the instructions for how to achieve a result (e.g. steps for making a pie crust within a pie recipe).
 * @see https://schema.org/HowToSection
 */
class HowToSection extends ListItem
{
    use CreativeWorkTrait ,
        ItemListTrait ;
}