<?php

namespace org\schema\organizations;

use org\schema\DefinedTerm;
use org\schema\Organization;

/**
 * Represents the collection of all sports organizations, including sports teams, governing bodies, and sports associations.
 *
 * @see https://schema.org/SportsOrganization
 */
class SportsOrganization extends Organization
{
    /**
     * A type of sport (e.g. Baseball).
     * @var string|array|DefinedTerm|null
     */
    public null|string|array|DefinedTerm $sport ;
}