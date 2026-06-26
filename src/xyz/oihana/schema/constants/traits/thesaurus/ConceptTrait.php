<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the SKOS relation and label property names of the
 * {@see \xyz\oihana\schema\thesaurus\Concept} schema entity.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a
 * single source of truth for hydration and serialization. It is aggregated,
 * through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait}, into the
 * global {@see \xyz\oihana\schema\constants\Oihana} constants class.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait ConceptTrait
{
    /**
     * The attribute key of the skos:broader property.
     */
    const string BROADER = 'broader' ;

    /**
     * The attribute key of the skos:broaderTransitive property.
     */
    const string BROADER_TRANSITIVE = 'broaderTransitive' ;

    /**
     * The attribute key of the skos:hiddenLabel property.
     */
    const string HIDDEN_LABEL = 'hiddenLabel' ;

    /**
     * The attribute key of the skos:narrower property.
     */
    const string NARROWER = 'narrower' ;

    /**
     * The attribute key of the skos:narrowerTransitive property.
     */
    const string NARROWER_TRANSITIVE = 'narrowerTransitive' ;

    /**
     * The attribute key of the skos:related property.
     */
    const string RELATED = 'related' ;

    /**
     * The attribute key of the skos:topConceptOf property.
     */
    const string TOP_CONCEPT_OF = 'topConceptOf' ;
}
