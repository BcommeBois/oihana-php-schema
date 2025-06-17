<?php

namespace org\schema;

use org\schema\creativeWork\SoftwareApplication;

/**
 * An entry point, within some Web-based protocol.
 * @see https://schema.org/EntryPoint
 */
class EntryPoint extends Intangible
{
    /**
     * An application that can complete the request.
     * @var SoftwareApplication|null
     */
    public null|SoftwareApplication $actionApplication ;

    /**
     * The high level platform(s) where the Action can be performed for the given URL.
     * To specify a specific application or operating system instance, use actionApplication.
     * @var null|string|DefinedTerm|Enumeration
     */
    public null|string|DefinedTerm|Enumeration $actionPlatform;

    /**
     * The supported content type(s) for an EntryPoint response.
     * @var string|null
     */
    public ?string $contentType;

    /**
     * The supported encoding type(s) for an EntryPoint request.
     * @var string|null
     */
    public ?string $encodingType;

    /**
     * An HTTP method that specifies the appropriate HTTP method for a request to an HTTP EntryPoint.
     * Values are capitalized strings as used in HTTP.
     * @var string|null
     */
    public ?string $httpMethod;

    /**
     * An url template (RFC6570) that will be used to construct the target of the execution of the action.
     * @var string|null
     */
    public ?string $urlTemplate;
}