<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * An media object file representation.
 */
class MediaObject extends CreativeWork
{
    /**
     * The bitrate of the item.
     */
    public ?float $bitrate ;

    /**
     * The contentSize of the item.
     */
    public ?float $contentSize ;

    /**
     * The contentUrl of the item.
     */
    public ?string $contentUrl ;

    /**
     * The duration of the item.
     */
    public ?int $duration ;

    /**
     * The embedUrl of the item.
     */
    public ?string $embedUrl ;

    /**
     * The height of the item.
     */
    public ?float $height ;

    /**
     * The playerType of the item.
     */
    public ?string $playerType ;

    /**
     * When multiple image appear in an entry, indicates which is primary. At most one image may be primary. Default value is false.
     */
    public ?bool $primary ;

    /**
     * The source available for this item
     */
    public ?array $source ;

    /**
     * The width of the item.
     */
    public ?float $width ;
}


