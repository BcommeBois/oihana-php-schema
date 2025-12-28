<?php

namespace xyz\oihana\schema\places;

use org\schema\DefinedTerm;
use org\schema\enumerations\DeliveryMethod;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Place;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a multi-functional operational site for an organization.
 *
 * A Site is a physical or logical place where one or more business functions
 * are performed (e.g. billing, delivery, construction, or customer service).
 * It extends {@see Place} and is intended to model real-world operational
 * locations within an organizational structure.
 *
 * This concept is commonly used to describe:
 * - customer sites
 * - provider or supplier sites
 * - operational, industrial, or service locations
 *
 * @see https://schema.org/Place
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\places
 * @since   1.0.2
 */
class Site extends Place
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The standardized procedure used to transfer a product or service
     * to its destination.
     *
     * This property may be expressed as a predefined {@see DeliveryMethod},
     * a {@see DefinedTerm}, a string identifier, or a list of such values.
     *
     * @var null|array|string|DeliveryMethod|DefinedTerm
     */
    public null|array|string|DeliveryMethod|DefinedTerm $deliveryMethod ;

    /**
     * The organization or person that owns or operates this site.
     *
     * This typically represents the legal or operational owner
     * of the location.
     *
     * @var int|string|Organization|Person|null
     */
    public null|int|string|Organization|Person $ownedBy ;

    /**
     * The position of this site within a sequence or ordered collection
     * of related sites.
     *
     * This can be used, for example, to express priority, display order,
     * or hierarchical positioning.
     *
     * @var int|string|null
     */
    public null|int|string $position ;
}