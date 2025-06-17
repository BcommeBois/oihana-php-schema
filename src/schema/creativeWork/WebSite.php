<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * A WebSite is a set of related web pages and other items typically served from a single web domain and accessible via URLs.
 * @see https://schema.org/WebSite
 */
class WebSite extends CreativeWork
{
    /**
     * The International Standard Serial Number (ISSN) that identifies this serial publication. You can repeat this property to identify different formats of, or the linking ISSN (ISSN-L) for, this serial publication.
     * @var string|null
     */
    public null|string $issn ;
}