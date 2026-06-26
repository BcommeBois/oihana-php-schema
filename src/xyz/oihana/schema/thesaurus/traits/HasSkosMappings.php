<?php

namespace xyz\oihana\schema\thesaurus\traits;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\thesaurus\Concept;

/**
 * SKOS mapping relations : links between concepts that belong to *different*
 * concept schemes.
 *
 * Where {@see HasSkosRelations} wires concepts together inside one scheme,
 * mapping relations align a local concept (e.g. a Proginov product category)
 * with an equivalent or related concept in an external taxonomy. They range
 * from the strongest claim (`exactMatch`) to the loosest (`relatedMatch`).
 *
 * Like the in-scheme relations they are nullable and traversal-only, and each
 * accepts a bare reference string, an AQL-projected associative array, or a
 * hydrated {@see Concept} (lists are hydrated through `#[HydrateWith]`).
 *
 * @see http://www.w3.org/2004/02/skos/core#exactMatch
 * @see http://www.w3.org/2004/02/skos/core#closeMatch
 * @see http://www.w3.org/2004/02/skos/core#broadMatch
 * @see http://www.w3.org/2004/02/skos/core#narrowMatch
 * @see http://www.w3.org/2004/02/skos/core#relatedMatch
 *
 * @package xyz\oihana\schema\thesaurus\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait HasSkosMappings
{
    /**
     * skos:broadMatch — a broader concept in another scheme.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $broadMatch ;

    /**
     * skos:closeMatch — a concept in another scheme that is sufficiently
     * similar to be used interchangeably in some applications.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $closeMatch ;

    /**
     * skos:exactMatch — a concept in another scheme that can be used
     * interchangeably across a wide range of applications (a stronger,
     * transitive `closeMatch`).
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $exactMatch ;

    /**
     * skos:narrowMatch — a narrower concept in another scheme.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $narrowMatch ;

    /**
     * skos:relatedMatch — an associatively related concept in another scheme.
     *
     * @var array<Concept>|string|null
     */
    #[ HydrateWith( Concept::class ) ]
    public null|string|array $relatedMatch ;
}
