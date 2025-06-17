<?php

namespace org\schema;

use org\schema\creativeWork\DefinedTermSet;

/**
 * A word, name, acronym, phrase, etc. with a formal definition. Often used in the context of category or subject classification, glossaries or dictionaries, product or creative work types, etc.
 * Use the name property for the term being defined, use termCode if the term has an alpha-numeric code allocated, use description to provide the definition of the term.
 * @see https://schema.org/DefinedTerm
 */
class DefinedTerm extends Intangible
{
    /**
     * A DefinedTermSet that contains this term.
     * @var string|DefinedTermSet|null
     */
    public null|string|DefinedTermSet $inDefinedTermSet ;

    /**
     * A code that identifies this DefinedTerm within a DefinedTermSet.
     * @var string|null
     */
    public null|string $termCode ;
}