<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait;
use xyz\oihana\schema\thesaurus\traits\HasSkosRelations;

/**
 * A SKOS concept : a schema.org {@see DefinedTerm} enriched with hierarchical
 * (SKOS) relations.
 *
 * SKOS (Simple Knowledge Organization System) is the W3C vocabulary for
 * thesauri and classification schemes. schema.org equates `DefinedTerm` with
 * `skos:Concept` and `DefinedTermSet` with `skos:ConceptScheme`, so a Concept
 * extends `DefinedTerm` rather than re-rooting a parallel hierarchy under
 * {@see \org\schema\Thing} : it reuses the existing bridge (`name`, `termCode`,
 * `inDefinedTermSet`, the ArangoDB metadata and the JSON-LD serialization) and
 * only adds what schema.org lacks — the broader/narrower relations.
 *
 * The relations are traversal-only (see {@see HasSkosRelations}).
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\Concept;
 *
 * $term = new Concept
 * ([
 *     'name'            => 'Red wine' ,
 *     'termCode'        => 'RED' ,
 *     Concept::BROADER  => 'wines/100' ,                 // a bare _key reference
 *     Concept::NARROWER =>
 *     [
 *         [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
 *     ],
 * ]);
 *
 * $term->narrower[ 0 ] instanceof Concept ; // true
 * ```
 *
 * @see DefinedTerm
 * @see HasSkosRelations
 * @see ConceptTrait
 * @see http://www.w3.org/2004/02/skos/core#Concept
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class Concept extends DefinedTerm
{
    use ConceptTrait ,
        HasSkosRelations ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}
