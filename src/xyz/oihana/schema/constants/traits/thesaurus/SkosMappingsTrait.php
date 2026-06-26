<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the SKOS mapping-relation property names carried by
 * {@see \xyz\oihana\schema\thesaurus\traits\HasSkosMappings}.
 *
 * Aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait SkosMappingsTrait
{
    /**
     * The attribute key of the skos:broadMatch property.
     */
    const string BROAD_MATCH = 'broadMatch' ;

    /**
     * The attribute key of the skos:closeMatch property.
     */
    const string CLOSE_MATCH = 'closeMatch' ;

    /**
     * The attribute key of the skos:exactMatch property.
     */
    const string EXACT_MATCH = 'exactMatch' ;

    /**
     * The attribute key of the skos:narrowMatch property.
     */
    const string NARROW_MATCH = 'narrowMatch' ;

    /**
     * The attribute key of the skos:relatedMatch property.
     */
    const string RELATED_MATCH = 'relatedMatch' ;
}
