<?php

namespace xyz\oihana\schema\constants\traits;

trait PaginationTrait
{
    /**
     * The attribute key for the 'limit' property.
     *
     * Used to identify the maximum number of items to return per page
     * in an array, JSON, or other structured representation.
     */
    public const string LIMIT = 'limit';

    /**
     * The attribute key for the 'maxLimit' property.
     *
     * Represents the upper bound on the number of items that can be requested per page.
     */
    public const string MAX_LIMIT = 'maxLimit';

    /**
     * The attribute key for the 'minLimit' property.
     *
     * Represents the lower bound on the number of items that can be requested per page.
     */
    public const string MIN_LIMIT = 'minLimit';

    /**
     * The attribute key for the 'offset' property.
     *
     * Indicates the number of items to skip before starting to collect the result set.
     */
    public const string OFFSET = 'offset';

    /**
     * The attribute key for the 'numberOfPages' property.
     *
     * Represents the total number of pages in the paginated collection.
     * Can be used to generate pagination controls or metadata.
     */
    public const string NUMBER_OF_PAGES = 'numberOfPages';

    /**
     * The attribute key for the 'page' property.
     *
     * Indicates the current page number in the paginated collection.
     */
    public const string PAGE = 'page';
}