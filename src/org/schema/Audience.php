<?php

namespace org\schema;

use org\schema\creativeWork\DefinedTermSet;
use org\schema\places\AdministrativeArea;

/**
 * Intended audience for an item, i.e. the group for whom the item was created.
 * @see https://schema.org/Audience
 */
class Audience extends Intangible
{
    /**
     * The target group associated with a given audience (e.g. veterans, car owners, musicians, etc.).
     * @var string|DefinedTermSet|null
     */
    public null|DefinedTermSet|string $audienceType ;

    /**
     * The geographic area associated with the audience.
     * @var null|string|Place|AdministrativeArea
     */
    public null|string|Place|AdministrativeArea $geographicArea ;
}