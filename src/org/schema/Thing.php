<?php

namespace org\schema ;

use JsonSerializable;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\traits\ArangoDBTrait;
use org\schema\traits\ThingTrait;

/**
 * The most generic type of item.
 * Represents any kind of item in a structured data model.
 *
 * Can be extended by more specific types such as `Person`, `Organization`, `Event`, etc.
 *
 * @see https://schema.org/Thing
 *
 * @package org\schema
 */
class Thing implements JsonSerializable
{
    use ArangoDBTrait,
        ThingTrait ;

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'https://schema.org' ;

    /**
     * The unique identifier of the item.
     */
    public null|int|string $id  ;

    /**
     * The name of the item.
     * @var int|string|null
     */
    public int|string|null $name ;

    /**
     * URL of the item.
     * @var int|string|null
     */
    public int|string|null $url ;

    /**
     * The active flag.
     */
    public ?bool $active ;

    /**
     * An additionalType for the item.
     */
    public array|string|null|object $additionalType ;

    /**
     * Date of creation of the resource.
     */
    public null|string $created ;

    /**
     * Date on which the resource was changed.
     */
    public null|string $modified ;

    /**
     * An alias for the item.
     */
    public string|object|array|null $alternateName ;

    /**
     * A short description of the item.
     */
    public string|object|array|null $description ;

    /**
     * A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.
     */
    public ?string $disambiguatingDescription ;

    /**
     * Indicates an item that this part of this item.
     * @var string|Thing|array<Thing>|null
     */
    public null|string|Thing|array $hasPart ;

    /**
     * The identifier of the item.
     */
    public ?string $identifier ;

    /**
     * Indicates a page (or other CreativeWork) for which this thing is the main entity being described.
     * @var string|null
     */
    public ?string $mainEntityOfPage ;

    /**
     * The image reference of this resource.
     * @var string|ImageObject|array<ImageObject|string>|null
     */
    public string|ImageObject|array|null $image ;

    /**
     * Indicates an item that this item is part of.
     * @var string|Thing|array<Thing>|null
     */
    public null|string|Thing|array $isPartOf ;

    /**
     * A legal document giving official permission to do something with the resource.
     * @var string|object|null
     */
    public string|object|null $license ;

    /**
     * The owner of this Thing.
     *
     * Represents any entity (person, organization, system, or other object)
     * that can be considered the possessor of this Thing.
     *
     * @var null|string|Thing
     */
    public null|string|Thing $owner ;

    /**
     * Indicates a potential Action, which describes an idealized action in which this thing would play an 'object' role.
     * @var array|Action|null
     */
    public null|array|Action $potentialAction ;

    /**
     * The publisher of the resource.
     * @var string|array<string|Person|Organization>|Person|Organization|null
     */
    public string|array|Person|Organization|null $publisher ;

    /**
     * URL of a reference Web page that unambiguously indicates the item's identity.
     * E.g. the URL of the item's Wikipedia page, Wikidata entry, or official website.
     * @var string|array|null
     */
    public string|array|null $sameAs ;

    /**
     * A CreativeWork or Event about this Thing.
     *
     * @var null|string|array|CreativeWork|Event
     */
    public null|string|array|CreativeWork|Event $subjectOf ;
}