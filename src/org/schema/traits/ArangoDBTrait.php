<?php

namespace org\schema\traits;

/**
 * The special ArangoDB attributes.
 * @see https://docs.arangodb.com/stable/concepts/data-structure/documents/#document-keys
 */
trait ArangoDBTrait
{
    /**
     * The metadata unique key identifier of the thing.
     */
    public null|string $_key  ;

    /**
     * The metadata identifier of the item.
     */
    public null|string $_id  ;

    /**
     * The metadata revision value of the thing.
     */
    public null|string $_rev ;

    /**
     * The metadata to indicates the edge 'from' identifier.
     * @var string|null
     */
    public null|string $_from ;

    /**
     * The metadata to indicates the edge 'to' identifier.
     * @var string|null
     */
    public null|string $_to ;
}


