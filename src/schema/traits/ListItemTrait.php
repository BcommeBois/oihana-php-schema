<?php

namespace org\schema\traits;

use org\schema\ListItem;
use org\schema\Thing;

/**
 * An list item, e.g. a step in a checklist or how-to description.
 * @see https://schema.org/ListItem
 */
trait ListItemTrait
{
    /**
     * An entity represented by an entry in a list or data feed (e.g. an 'artist' in a list of 'artists').
     */
    public ?Thing $item ;

    /**
     * A link to the ListItem that follows the current one.
     * @var null|ListItem
     */
    public null|ListItem $nextItem ;

    /**
     * The position of an item in a series or sequence of items.
     * @var null|int|string
     */
    public null|int|string $position ;

    /**
     * A link to the ListItem that precedes the current one.
     * @var null|ListItem
     */
    public null|ListItem $previousItem ;
}