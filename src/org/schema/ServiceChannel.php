<?php

namespace org\schema;

/**
 * A service provided by an organization, e.g. delivery service, print services, etc.
 * @see https://schema.org/Service
 */
class ServiceChannel extends Intangible
{
    /**
     * A language someone may use with or at the item, service or place. Please use one of the language codes from the IETF BCP 47 standard
     * @var null|array|Language|string
     */
    public null|array|Language|string $availableLanguage ;

    /**
     * Estimated processing time for the service using this channel.
     * @var Duration|int|null
     */
    public null|Duration|int $processingTime ;

    /**
     * The service provided by this channel.
     * @var Service|null
     */
    public null|Service $providesService ;

    /**
     * The location (e.g. civic structure, local business, etc.) where a person can go to access the service.
     * @var Place|null
     */
    public null|Place $serviceLocation ;

    /**
     * The phone number to use to access the service.
     * @var ContactPoint|null|string
     */
    public null|ContactPoint|string $servicePhone ;

    /**
     * The address for accessing the service by mail.
     * @var PostalAddress|null|string
     */
    public null|PostalAddress|string $servicePostalAddress ;

    /**
     * The number to access the service by text message.
     * @var ContactPoint|null|string
     */
    public null|ContactPoint|string $serviceSmsNumber ;

    /**
     * The website to access the service.
     * @var null|array|ContactPoint|string
     */
    public null|array|ContactPoint|string $serviceUrl ;
}