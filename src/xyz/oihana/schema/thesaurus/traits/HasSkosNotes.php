<?php

namespace xyz\oihana\schema\thesaurus\traits;

/**
 * SKOS documentation notes for a concept.
 *
 * These are the free-text annotations SKOS defines to document the meaning and
 * the editorial history of a concept. They complement the schema.org fields
 * already inherited from {@see \org\schema\Thing} : `skos:definition` maps to
 * `description`, `skos:prefLabel` to `name`, `skos:altLabel` to `alternateName`.
 *
 * Each note accepts a plain `string` or an `array` (e.g. one entry per language
 * tag). All are nullable.
 *
 * @see http://www.w3.org/2004/02/skos/core#note
 * @see http://www.w3.org/2004/02/skos/core#changeNote
 * @see http://www.w3.org/2004/02/skos/core#editorialNote
 * @see http://www.w3.org/2004/02/skos/core#example
 * @see http://www.w3.org/2004/02/skos/core#historyNote
 * @see http://www.w3.org/2004/02/skos/core#scopeNote
 *
 * @package xyz\oihana\schema\thesaurus\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait HasSkosNotes
{
    /**
     * skos:changeNote — a note about a modification to the concept.
     *
     * @var string|array|null
     */
    public string|array|null $changeNote ;

    /**
     * skos:editorialNote — a note for editors, translators or maintainers.
     *
     * @var string|array|null
     */
    public string|array|null $editorialNote ;

    /**
     * skos:example — an example of the use of the concept.
     *
     * @var string|array|null
     */
    public string|array|null $example ;

    /**
     * skos:historyNote — a note about the past state or use of the concept.
     *
     * @var string|array|null
     */
    public string|array|null $historyNote ;

    /**
     * skos:note — a general-purpose note.
     *
     * @var string|array|null
     */
    public string|array|null $note ;

    /**
     * skos:scopeNote — a note clarifying the intended meaning and boundaries
     * of the concept.
     *
     * @var string|array|null
     */
    public string|array|null $scopeNote ;
}
