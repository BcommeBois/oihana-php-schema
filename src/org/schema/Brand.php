<?php

namespace org\schema;

/**
 * A brand is a name used by an organization or business person for labeling a product, product group, or similar.
 * @see http://schema.org/Brand
 */
class Brand extends Thing
{
    /**
     * The additional description of the organization.
     * Note : this property is a custom attribute of the original Organization class defined in http://schema.org/Organization.
     */
    public string|array|object|null $additional ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

    /**
     * Photographs of this organization (legacy spelling; see singular form, photo).
     */
    public ?array $images ;

    /**
     * An associated logo
     */
    public string|object|null $logo ;

    /**
     * Photographs of this organization (legacy spelling; see singular form, photo).
     */
    public ?array $photos ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event.
     */
    public array|Person|Organization|null $sponsor ;
}


