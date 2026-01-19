<?php

namespace xyz\oihana\schema;

use org\schema\Intangible;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\PaginationTrait;

/**
 * Represents pagination metadata.
 *
 * This class defines structured pagination information for a collection of items, such as
 * limits, offsets, and page numbers, following the {@see Oihana::SCHEMA} context for
 * JSON-LD interoperability.
 *
 * It extends the {@see Intangible} type from Schema.org and uses {@see PaginationTrait}
 * to provide constants and helper methods related to paginated collections.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\Pagination;
 *
 * $pagination = new Pagination();
 * $pagination->page          = 2;
 * $pagination->limit         = 20;
 * $pagination->numberOfPages = 10;
 * $pagination->offset        = 20;
 *
 * print_r($pagination);
 * // Pagination Object
 * // (
 * //     [page]          => 2
 * //     [limit]         => 20
 * //     [numberOfPages] => 10
 * //     [offset]        => 20
 * // )
 * ```
 *
 * This schema can be used to describe paginated datasets in structured formats
 * such as JSON-LD, APIs, or linked data, ensuring interoperability with other schema-based models.
 *
 * @see https://schema.org/Intangible
 *
 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.1
 */
class Pagination extends Intangible
{
    use PaginationTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

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