<?php

namespace org\schema;

use org\schema\creativeWork\CategoryCodeSet;

/**
 * A Category Code.
 * @see https://schema.org/CategoryCode
 */
class CategoryCode extends DefinedTerm
{
    /**
     * A short textual code that uniquely identifies the value.
     * @var null|string|int
     */
    public null|string|int $codeValue ;

    /**
     * A DefinedTermSet that contains this term.
     * @var string|CategoryCodeSet|null
     */
    public null|string|CategoryCodeSet $inCodeSet ;
}