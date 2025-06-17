<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * An audio object file representation.
 * @see https://schema.org/Chapter
 */
class Chapter extends CreativeWork
{
    /**
     * The page on which the work ends; for example "138" or "xvi".
     * @var object|string|null
     */
    public null|object|string $pageEnd ;

    /**
     * The page on which the work starts; for example "135" or "xiii".
     * @var object|string|null
     */
    public null|object|string $pageStart ;

    /**
     * Any description of pages that is not separated into pageStart and pageEnd;
     * for example, "1-6, 9, 55" or "10-12, 46-49".
     * @var string|null
     */
    public ?string $pagination ;
}


