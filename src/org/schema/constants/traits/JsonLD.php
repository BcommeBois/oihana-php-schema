<?php

namespace org\schema\constants\traits;

/**
 * JSON-LD is a method of using JSON to express linked data.
 *
 * @see https://niem.github.io/json/reference/json-ld/
 */
trait JsonLD
{
    /**
     * Any JSON object may carry a key @context, which indicates a context that is active
     * at the scope of that object, and within to nested objects.
     *
     * @see https://niem.github.io/json/reference/json-ld/context
     */
    const string AT_CONTEXT = '@context' ;

    /**
     * JSON-LD supports references to objects with a reserved key @id which can be used either as an object identifier
     * or a reference to an object identifier. The value of @id must be a string.
     *
     * @see https://niem.github.io/json/reference/json-ld/identifiers/
     */
    const string AT_ID = '@id'      ;

    /**
     * The type of the object.
     */
    const string AT_TYPE = '@type'    ;
}