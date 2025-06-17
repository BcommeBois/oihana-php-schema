<?php

namespace org\schema\creativeWork\medias ;

use org\schema\creativeWork\MediaObject;

/**
 * A video object file representation.
 */
class VideoObject extends MediaObject
{
    /**
     * The actor of the item
     */
    public ?string $actor ;

    /**
     * The audio codec
     */
    public ?string $audioCodec ;

    /**
     * The audio bit per sample
     */
    public ?int $bitsPerSample ;

    /**
     * The caption of the item.
     */
    public ?string $caption ;

    /**
     * The number of channel of the audio
     */
    public ?string $channels ;

    /**
     * The director of the item.
     */
    public ?string $director ;

    /**
     * The music by of the item
     */
    public ?string $musicBy ;

    /**
     * The sample rate of the audio
     */
    public ?int $sampleRate ;

    /**
     * The thumbnail of the item.
     */
    public string|object|null $thumbnail ;

    /**
     * The transcript of the item
     */
    public ?string $transcript ;

    /**
     * The video frame size of the item
     */
    public ?string $videoFrameSize ;

    /**
     * The video quality of the item
     */
    public ?string $videoQuality ;
}


