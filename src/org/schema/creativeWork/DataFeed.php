<?php

namespace org\schema\creativeWork ;

use org\schema\DataFeedItem;
use org\schema\Thing;

/**
 * A single feed providing structured information about one or more entities or topics.
 * @see https://schema.org/DataFeed
 */
class DataFeed extends DataSet
{
    /**
     * An item within a data feed. Data feeds may have many elements.
     * @var null|array|string|Thing|DataFeedItem
     */
    public null|array|string|Thing|DataFeedItem $dataFeedElement ;
}


