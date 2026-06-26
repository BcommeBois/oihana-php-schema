<?php

namespace xyz\oihana\schema\thesaurus\traits;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\thesaurus\Concept;

/**
 * SKOS hierarchical (semantic) relations between concepts.
 *
 * All properties are nullable and **traversal-only** : they are never persisted
 * and never harvested. They are populated only on selected API responses (e.g.
 * `/{key}/children`, `/descendants`, `/ancestors`) to expose the concept tree
 * without storing it on the document.
 *
 * Each relation accepts, interchangeably :
 * - `null` — the relation is not loaded ;
 * - a `string` — a bare reference (e.g. an ArangoDB `_key`) ;
 * - an associative `array` — an AQL-projected concept, rebuildable via
 *   `new Concept( $array )` ;
 * - a hydrated {@see Concept} (singular relations) or a list of them (plural
 *   relations, hydrated element by element through `#[HydrateWith]`).
 *
 * Children are intentionally hydrated as the base {@see Concept} (not as a leaf
 * subtype) : for a pure traversal payload, a node only needs to carry its key,
 * name and code.
 *
 * @see http://www.w3.org/2004/02/skos/core#broader
 * @see http://www.w3.org/2004/02/skos/core#narrower
 * @see http://www.w3.org/2004/02/skos/core#broaderTransitive
 * @see http://www.w3.org/2004/02/skos/core#narrowerTransitive
 * @see http://www.w3.org/2004/02/skos/core#related
 *
 * @package xyz\oihana\schema\thesaurus\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait HasSkosRelations
{
    /**
     * skos:broader — the direct parent concept (the narrower-to-broader link).
     *
     * @var null|string|array|Concept
     */
    public null|string|array|Concept $broader ;

    /**
     * skos:broaderTransitive — every ancestor concept (the transitive closure
     * of `broader`), typically ordered from the nearest parent to the root.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $broaderTransitive ;

    /**
     * skos:narrower — the direct child concepts.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $narrower ;

    /**
     * skos:narrowerTransitive — every descendant concept (the transitive
     * closure of `narrower`).
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $narrowerTransitive ;

    /**
     * skos:related — associative (non-hierarchical) links to sibling concepts.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $related ;
}
