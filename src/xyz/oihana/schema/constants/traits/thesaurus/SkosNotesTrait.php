<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the SKOS documentation-note property names carried by
 * {@see \xyz\oihana\schema\thesaurus\traits\HasSkosNotes}.
 *
 * Aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait SkosNotesTrait
{
    /**
     * The attribute key of the skos:changeNote property.
     */
    const string CHANGE_NOTE = 'changeNote' ;

    /**
     * The attribute key of the skos:editorialNote property.
     */
    const string EDITORIAL_NOTE = 'editorialNote' ;

    /**
     * The attribute key of the skos:example property.
     */
    const string EXAMPLE = 'example' ;

    /**
     * The attribute key of the skos:historyNote property.
     */
    const string HISTORY_NOTE = 'historyNote' ;

    /**
     * The attribute key of the skos:note property.
     */
    const string NOTE = 'note' ;

    /**
     * The attribute key of the skos:scopeNote property.
     */
    const string SCOPE_NOTE = 'scopeNote' ;
}
