<?php

namespace org\schema ;

use DateTime;
use JsonSerializable;
use org\schema\traits\helpers\ThingTrait;

class Thing implements JsonSerializable
{
    use ThingTrait ;

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
     * The name of the item.
     * @var int|string|null
     */
    public int|string|null $name ;

    /**
     * An alias for the item.
     */
    public string|object|null $alternateName ;

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
    public null|string|DateTime $created ;

    /**
     * A short description of the item.
     */
    public ?string $description ;

    /**
     * Indicates an item that this part of this item.
     * @var string|Thing|array|null
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
     */
    public string|object|null $image ;

    /**
     * Indicates an item that this item is part of.
     * @var string|Thing|array|null
     */
    public null|string|Thing|array $isPartOf ;

    /**
     * A legal document giving official permission to do something with the resource.
     */
    public string|object|null $license ;

    /**
     * Date on which the resource was changed.
     */
    public null|string|DateTime $modified ;

    /**
     * The publisher of the resource.
     * @var string|array|Person|Organization|null
     */
    public string|array|Person|Organization|null $publisher ;
}