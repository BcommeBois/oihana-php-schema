<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * A business entity type is a conceptual entity representing the legal form, the size, the main line of business, the position in the value chain, or any combination thereof, of an organization or business person.
 * Commonly used values:
 * @see https://schema.org/BusinessEntityType
 */
class BusinessEntityType extends Enumeration
{
    public const string BUSINESS           = 'http://purl.org/goodrelations/v1#Business';
    public const string END_USER           = 'http://purl.org/goodrelations/v1#Enduser';
    public const string PUBLIC_INSTITUTION = 'http://purl.org/goodrelations/v1#PublicInstitution';
    public const string RESELLER           = 'http://purl.org/goodrelations/v1#Reseller';
}