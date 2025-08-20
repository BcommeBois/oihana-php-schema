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
     * The number of items to return per page.
     *
     * Can be null if unspecified. Used to limit the number of items returned
     * in a paginated collection.
     *
     * @var int|null
     */
    public null|int $limit ;

    /**
     * The maximum allowed number of items per page.
     *
     * This can be used to enforce an upper bound on the `limit` value.
     *
     * @var int|null
     */
    public null|int $maxLimit ;


    /**
     * The minimum allowed number of items per page.
     *
     * This can be used to enforce a lower bound on the `limit` value.
     *
     * @var int|null
     */
    public null|int $minLimit ;

    /**
     * The number of pages in the paginated collection.
     * @var int|null
     * @see https://schema.org/numberOfPages
     */
    public null|int $numberOfPages ;

    /**
     * The number of items to skip before starting to collect the result set.
     *
     * Useful for paginated collections where items are retrieved in segments.
     *
     * @var int|null
     */
    public null|int $offset ;

    /**
     * The current page number in the paginated collection.
     *
     * Starts at 1 by convention. Can be null if not specified.
     *
     * @var int|null
     */
    public null|int $page ;
}