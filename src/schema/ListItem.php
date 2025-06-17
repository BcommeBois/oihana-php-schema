<?php

namespace org\schema;

use org\schema\traits\ListItemTrait;

/**
 * An list item, e.g. a step in a checklist or how-to description.
 * @see https://schema.org/ListItem
 */
class ListItem extends Intangible
{
    use ListItemTrait ;
}