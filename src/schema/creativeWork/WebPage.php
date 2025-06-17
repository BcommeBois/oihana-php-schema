<?php

namespace org\schema\creativeWork ;

use DateTime;
use org\schema\CreativeWork;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\creativeWork\web\BreadcrumbList;
use org\schema\creativeWork\web\WebPageElement;
use org\schema\DefinedTerm;
use org\schema\enumerations\Specialty;
use org\schema\Organization;
use org\schema\Person;

/**
 * A web page. Every web page is implicitly assumed to be declared to be of type WebPage, so the various properties about that webpage, such as breadcrumb may be used. We recommend explicit declaration if these properties are specified, but if they are found outside of an itemscope, they will be assumed to be about the page.
 * @see https://schema.org/WebPage
 */
class WebPage extends CreativeWork
{
    /**
     * A set of links that can help a user understand and navigate a website hierarchy.
     * @var array|string|BreadcrumbList|null
     */
    public null|array|string|BreadcrumbList $breadcrumb ;

    /**
     * Date on which the content on this web page was last reviewed for accuracy and/or completeness.
     * @var null|string|DateTime
     */
    public null|string|DateTime $lastReviewed ;

    /**
     * Indicates if this web page element is the main subject of the page.
     * @var WebPageElement|null
     */
    public ?WebPageElement $mainContentOfPage ;

    /**
     * Indicates the main image on the page.
     * @var ImageObject|null
     */
    public ?ImageObject $primaryImageOfPage ;

    /**
     * A link related to this web page, for example to other related web pages.
     * @var string|null
     */
    public ?string $relateLink ;

    /**
     * People or organizations that have reviewed the content on this web page for accuracy and/or completeness.
     * @var array|Person|Organization|null
     */
    public null|array|Person|Organization $reviewedBy ;

    /**
     * One of the more significant URLs on the page.
     * Typically, these are the non-navigation links that are clicked on the most.
     * @var string|null
     */
    public ?string $significantLink ;

    /**
     * Indicates sections of a Web page that are particularly 'speakable' in the sense of being highlighted as being especially appropriate for text-to-speech conversion.
     * Other sections of a page may also be usefully spoken in particular circumstances; the 'speakable' property serves to indicate the parts most likely to be generally useful for speech.
     * @var string|array|null|SpeakableSpecification
     */
    public null|array|string|SpeakableSpecification $speakable;

    /**
     * One of the domain specialities to which this web page's content applies.
     * @var array|string|DefinedTerm|Specialty|null
     */
    public null|array|string|DefinedTerm|Specialty $specialty ;
}


