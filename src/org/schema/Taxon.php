<?php

namespace org\schema;

/**
 * A set of organisms asserted to represent a natural cohesive biological unit.
 * @see https://schema.org/Taxon
 */
class Taxon extends Thing
{
    /**
     * Closest child taxa of the taxon in question.
     * @var string|array|Taxon|null
     */
    public null|array|string|Taxon $childTaxon ;

    /**
     * A Defined Term contained in this term set.
     * @var array|DefinedTerm|null
     */
    public null|array|DefinedTerm $hasDefinedTerm ;

    /**
     * Closest parent taxon of the taxon in question.
     * @var string|array|Taxon|null
     */
    public null|array|string|Taxon $parentTaxon ;

    /**
     * The taxonomic rank of this taxon given preferably as a URI from a controlled vocabulary – typically the ranks from TDWG TaxonRank ontology or equivalent Wikidata URIs.
     * @var string|PropertyValue|null
     */
    public null|string|PropertyValue $taxonRank ;
}