<?php

namespace org\schema\creativeWork\medias ;

use org\schema\creativeWork\MediaObject;

/**
 * An image object file representation.
 */
class ImageObject extends MediaObject
{
    /**
     * The caption of the item.
     */
    public ?string $caption ;

    /**
     * The exif data of the item.
     */
    public ?string $exifData ;

    /**
     * The thumbnail of the item.
     */
    public string|object|null $thumbnail ;
}


