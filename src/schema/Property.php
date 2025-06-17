<?php

namespace org\schema;

/**
 * A property, used to indicate attributes and relationships of some Thing; equivalent to rdf:Property.
 * @see https://schema.org/Property
 */
class Property extends StructuredValue
{
    /**
     * Relates a property to a class that is (one of) the type(s) the property is expected to be used on.
     * @var Type|null
     */
    public null|Type $domainIncludes ;

    /**
     * Relates a property to a property that is its inverse. Inverse properties relate the same pairs of items to each other, but in reversed direction. For example, the 'alumni' and 'alumniOf' properties are inverseOf each other.
     * @var Property|null
     */
    public null|Property $inverseOf ;

    /**
     * Relates a property to a class that constitutes (one of) the expected type(s) for values of the property.
     * @var array|Type|null
     */
    public null|array|Type $rangeIncludes ;

    /**
     * Relates a term (i.e. a property, class or enumeration) to one that supersedes it.
     * @var Type|Enumeration|Property|null
     */
    public null|Type|Enumeration|Property $supersededBy ;
}