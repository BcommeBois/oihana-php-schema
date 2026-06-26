<?php

namespace xyz\oihana\schema\thesaurus;

use oihana\reflect\attributes\HydrateWith;

/**
 * A SKOS ordered collection : a {@see Collection} whose members carry a
 * meaningful order.
 *
 * Use it instead of a plain {@see Collection} when the sequence matters (e.g. a
 * ranked or curated list). The order is held by {@see OrderedCollection::$memberList};
 * the inherited {@see Collection::$member} stays available for the unordered
 * view of the same members.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\OrderedCollection;
 *
 * $ranking = new OrderedCollection
 * ([
 *     'name'                          => 'Top sellers' ,
 *     OrderedCollection::MEMBER_LIST =>
 *     [
 *         [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
 *         [ 'name' => 'Merlot'             , 'termCode' => 'MERLOT'   ] ,
 *     ],
 * ]);
 * ```
 *
 * @see Collection
 * @see http://www.w3.org/2004/02/skos/core#OrderedCollection
 * @see http://www.w3.org/2004/02/skos/core#memberList
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class OrderedCollection extends Collection
{
    /**
     * skos:memberList — the members in a meaningful order (an `rdf:List` in
     * SKOS). Members are concepts and/or nested collections.
     *
     * @var array<Concept|Collection>|string|null
     */
    #[ HydrateWith( Concept::class , Collection::class ) ]
    public null|string|array $memberList ;
}
