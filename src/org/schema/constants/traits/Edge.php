<?php

namespace org\schema\constants\traits;

/**
 * In the ArangoDB databases, all edge contain special attributes at the top-level
 * that start with an underscore, known as system attributes.
 *
 * @see https://docs.arangodb.com/stable/concepts/data-structure/documents/
 */
trait Edge
{
    /**
     * The document identifier of the source vertex stored in the _from attribute.
     */
    const string _FROM  = '_from' ;

    /**
     * The document identifier of the target vertex stored in the _to attribute.
     */
    const string _TO  = '_to' ;
}