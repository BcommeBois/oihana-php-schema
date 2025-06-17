<?php

namespace org\schema\creativeWork\medias ;

use org\schema\creativeWork\MediaObject;

/**
 * An audio object file representation.
 */
class AudioObject extends MediaObject
{
    /**
     * The audio codec
     */
    public ?string $audioCodec ;

    /**
     * The audio bit per sample
     */
    public ?int $bitsPerSample ;

    /**
     * The caption for this object
     */
    public ?string $caption ;

    /**
     * The number of channel of the audio
     * @var ?string
     */
    public ?string $channels ;

    /**
     * The sample rate of the audio
     */
    public ?string $sampleRate ;

    /**
     * The thumbnail of the object
     */
    public ?string $thumbnail ;

    /**
     * The transcript of the item.
     */
    public ?string $transcript ;
}


