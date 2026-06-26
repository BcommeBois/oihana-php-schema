<?php

namespace xyz\oihana\schema\thesaurus\traits;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\thesaurus\Collection;
use xyz\oihana\schema\thesaurus\Concept;

/**
 * SKOS membership of a concept collection.
 *
 * A SKOS collection gathers concepts that belong together without being part of
 * the hierarchy (see {@see HasSkosRelations} for the hierarchy). Its `member`
 * list is **polymorphic** : a member is either a {@see Concept} or a nested
 * {@see Collection}. The reflection hydrator dispatches each member on its
 * `@type` discriminator (falling back to {@see Concept} otherwise).
 *
 * Like the other SKOS links, `member` is nullable and traversal-only, and
 * accepts a bare reference string, an AQL-projected associative array, or a
 * hydrated object.
 *
 * @see http://www.w3.org/2004/02/skos/core#member
 *
 * @package xyz\oihana\schema\thesaurus\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait HasSkosMembers
{
    /**
     * skos:member — the concepts and/or nested collections this collection
     * gathers.
     *
     * @var array<Concept|Collection>|string|null
     */
    #[ HydrateWith( Concept::class , Collection::class ) ]
    public null|string|array $member ;
}
