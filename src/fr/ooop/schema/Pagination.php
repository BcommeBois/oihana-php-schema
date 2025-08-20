<?php

namespace fr\ooop\schema;

use org\schema\Intangible;

/**
 * Represents pagination information for a collection of items.
 *
 * Extends `Intangible` as defined in schema.org.
 *
 * This class can be used to describe limits, offsets, and bounds for paginated collections
 * in JSON-LD or structured data.
 *
 * @see https://schema.org/Intangible
 * @package fr\ooop\schema
 */
class Pagination extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = 'https://schema.ooop.fr';

    /**
     * The 'limit' key.
     * @var int|null
     */
    public null|int $limit ;

    /**
     * The 'maxLimit' key.
     * @var int|null
     */
    public null|int $maxLimit ;

    /**
     * The 'minLimit' key.
     * @var int|null
     */
    public null|int $minLimit ;

    /**
     * The 'offset' key.
     * @var int|null
     */
    public null|int $offset ;
}