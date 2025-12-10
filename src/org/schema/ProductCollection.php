<?php

namespace org\schema;

use org\schema\traits\CollectionTrait;
use org\schema\traits\CreativeWorkTrait;

/**
 * Any offered product or service. For example: a pair of shoes; a concert ticket;
 * the rental of a car; a haircut; or an episode of a TV show streamed online.
 *
 * @see https://schema.org/Product
 */
class ProductCollection extends Product
{
    use CreativeWorkTrait ,
        CollectionTrait ;

    /**
     * This links to a node or nodes indicating the exact quantity of the products included in an Offer or ProductCollection.
     * @var array|TypeAndQuantityNode|null
     */
    public null|array|TypeAndQuantityNode $includesObject ;
}