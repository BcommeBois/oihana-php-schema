<?php

namespace org\schema;

use DateTime;

/**
 * A single item within a larger data feed.
 * @see https://schema.org/DataFeedItem
 */
class DataFeedItem extends Intangible
{
    /**
     * The date on which the CreativeWork was created or the item was added to a DataFeed.
     */
    public null|string|DateTime $dateCreated ;

    /**
     * The datetime the item was removed from the DataFeed.
     */
    public null|string|DateTime $dateDeleted ;

    /**
     * The date on which the CreativeWork was most recently modified or when the item's entry was modified within a DataFeed.
     */
    public null|string|DateTime $dateModified ;

    /**
     * An entity represented by an entry in a list or data feed (e.g. an 'artist' in a list of 'artists').
     * @var Thing|null
     */
    public ?Thing $item ;
}