<?php

namespace org\schema\creativeWork ;

use org\schema\creativeWork\enumerations\MeasurementMethodEnum;
use org\schema\creativeWork\medias\DataDownload;
use org\schema\DataFeedItem;
use org\schema\DefinedTerm;
use org\schema\Property;
use org\schema\PropertyValue;
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


