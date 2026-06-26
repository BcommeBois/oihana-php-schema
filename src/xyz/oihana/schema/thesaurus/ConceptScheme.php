<?php

namespace xyz\oihana\schema\thesaurus;

use oihana\reflect\attributes\HydrateWith;

use org\schema\creativeWork\DefinedTermSet;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptSchemeTrait;

/**
 * A SKOS concept scheme : a schema.org {@see DefinedTermSet} that exposes its
 * root concepts.
 *
 * schema.org equates `DefinedTermSet` with `skos:ConceptScheme`, so a concept
 * scheme extends `DefinedTermSet` and reuses its `hasDefinedTerm` member list.
 * It only adds what schema.org lacks — the entry points into the hierarchy
 * (`hasTopConcept`), the inverse of {@see Concept::$topConceptOf}.
 *
 * Scheme membership of a concept is already carried by the inherited
 * `DefinedTerm::$inDefinedTermSet` property, which maps to `skos:inScheme`.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\ConceptScheme;
 *
 * $scheme = new ConceptScheme
 * ([
 *     'name'                       => 'Product categories' ,
 *     ConceptScheme::HAS_TOP_CONCEPT =>
 *     [
 *         [ 'name' => 'Wines' , 'termCode' => 'WINE' ] ,
 *         [ 'name' => 'Spirits' , 'termCode' => 'SPIRIT' ] ,
 *     ],
 * ]);
 * ```
 *
 * @see DefinedTermSet
 * @see Concept
 * @see ConceptSchemeTrait
 * @see http://www.w3.org/2004/02/skos/core#ConceptScheme
 * @see http://www.w3.org/2004/02/skos/core#hasTopConcept
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class ConceptScheme extends DefinedTermSet
{
    use ConceptSchemeTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * skos:hasTopConcept — the top (root) concepts of the scheme.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $hasTopConcept ;
}
