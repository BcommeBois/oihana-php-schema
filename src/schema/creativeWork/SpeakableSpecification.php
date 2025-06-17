<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * A SpeakableSpecification indicates (typically via xpath or cssSelector) sections of a document that are highlighted as particularly speakable.
 * Instances of this type are expected to be used primarily as values of the speakable property.
 * @see https://schema.org/SpeakableSpecification
 */
class SpeakableSpecification extends CreativeWork
{
    /**
     * A CSS selector, e.g. of a SpeakableSpecification or WebPageElement.
     * In the latter case, multiple matches within a page can constitute a single conceptual "Web page element".
     * @var string|null
     */
    public null|string $cssSelector ;

    /**
     * An XPath, e.g. of a SpeakableSpecification or WebPageElement.
     * In the latter case, multiple matches within a page can constitute a single conceptual "Web page element".
     * @var null|string
     */
    public null|string $xpath ;
}


