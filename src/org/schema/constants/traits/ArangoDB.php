<?php

namespace org\schema\constants\traits;

/**
 * In the ArangoDB databases, all documents contain special attributes at the top-level
 * that start with an underscore, known as system attributes.
 *
 * @see https://docs.arangodb.com/stable/concepts/data-structure/documents/
 */
trait ArangoDB
{
    use Edge ;

    /**
     * The document identifier is stored as a string in the _id attribute.
     */
    const string _ID = '_id' ;

    /**
     * The document key is stored as a string in the _key attribute.
     */
    const string _KEY = '_key' ;

    /**
     * Every document in ArangoDB has a revision, stored in the system attribute _rev.
     * It is fully managed by the server and read-only for the user.
     */
    const string _REV = '_rev' ;
}