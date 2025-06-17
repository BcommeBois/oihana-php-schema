<?php

namespace org\schema\creativeWork;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;

/**
 * A set of defined terms, for example a set of categories or a classification scheme, a glossary, dictionary or enumeration.
 * @see https://schema.org/DefinedTermSet
 */
class DefinedTermSet extends CreativeWork
{
    /**
     * A list of Defined Term contained in this term set.
     */
    public null|array|DefinedTerm $hasDefinedTerm ;
}