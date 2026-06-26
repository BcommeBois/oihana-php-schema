<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\CollectionTrait;
use xyz\oihana\schema\thesaurus\traits\HasSkosMembers;

/**
 * A SKOS collection : a labelled, **non-hierarchical** grouping of concepts.
 *
 * Where a {@see ConceptScheme} is a whole vocabulary and the broader/narrower
 * relations form its tree, a collection simply gathers concepts that belong
 * together for a reason that is not subsumption — e.g. "featured grape
 * varieties". It is **not** a {@see Concept} and carries no hierarchy.
 *
 * It extends {@see Intangible} (like the schema.org `ItemList`), so it inherits
 * `name`, `description` and the ArangoDB metadata. Members are exposed through
 * {@see HasSkosMembers::$member} and may themselves be nested collections.
 *
 * For an explicitly ordered grouping, use {@see OrderedCollection}.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\Collection;
 *
 * $collection = new Collection
 * ([
 *     'name'             => 'Featured grape varieties' ,
 *     Collection::MEMBER =>
 *     [
 *         [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
 *         [ 'name' => 'Merlot'             , 'termCode' => 'MERLOT'   ] ,
 *     ],
 * ]);
 * ```
 *
 * @see Intangible
 * @see Concept
 * @see OrderedCollection
 * @see HasSkosMembers
 * @see CollectionTrait
 * @see http://www.w3.org/2004/02/skos/core#Collection
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class Collection extends Intangible
{
    use CollectionTrait ,
        HasSkosMembers ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}
