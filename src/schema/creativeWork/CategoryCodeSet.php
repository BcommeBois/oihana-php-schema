<?php

namespace org\schema\creativeWork;

use org\schema\CategoryCode;
use org\schema\CreativeWork;

/**
 * A set of Category Code values.
 * @see https://schema.org/CategoryCodeSet
 */
class CategoryCodeSet extends CreativeWork
{
    /**
     * A list of Category code contained in this code set.
     */
    public null|array|CategoryCode $hasCategoryCode ;
}