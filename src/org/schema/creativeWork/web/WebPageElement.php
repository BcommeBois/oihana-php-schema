<?php

namespace org\schema\creativeWork\web ;

use org\schema\CreativeWork;

/**
 * A web page element, like a table or an image.
 * @see https://schema.org/WebPageElement
 */
class WebPageElement extends CreativeWork
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


