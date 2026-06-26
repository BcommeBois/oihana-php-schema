<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\thesaurus\ConceptScheme} schema entity.
 *
 * Aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait ConceptSchemeTrait
{
    /**
     * The attribute key of the skos:hasTopConcept property.
     */
    const string HAS_TOP_CONCEPT = 'hasTopConcept' ;
}
