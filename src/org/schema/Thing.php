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
     * The name of the item.
     * @var int|string|null
     */
    public int|string|null $name ;

    /**
     * An alias for the item.
     */
    public string|object|null $alternateName ;

    /**
     * A short description of the item.
     */
    public ?string $description ;

    /**
     * Indicates an item that this part of this item.
     * @var string|Thing|array<Thing>|null
     */
    public null|string|Thing|array $hasPart ;

    /**
     * A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.
     */
    public ?string $disambiguatingDescription ;

    /**
     * The identifier of the item.
     */
    public ?string $identifier ;

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
     * The publisher of the resource.
     * @var string|array<string|Person|Organization>|Person|Organization|null
     */
    public string|array|Person|Organization|null $publisher ;
}