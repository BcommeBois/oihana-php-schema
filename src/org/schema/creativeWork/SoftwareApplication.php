<?php

namespace org\schema\creativeWork;

use org\schema\CreativeWork;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\DefinedTerm;
use org\schema\places\Country;

/**
 * A software application.
 * @see https://schema.org/SoftwareApplication
 */
class SoftwareApplication extends CreativeWork
{
    /**
     * Type of software application, e.g. 'Game, Multimedia'.
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $applicationCategory;

    /**
     * Subcategory of the application, e.g. 'Arcade Game'.
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $applicationSubCategory ;

    /**
     * The name of the application suite to which the application belongs (e.g. Excel belongs to Office).
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $applicationSuite ;

    /**
     * Device required to run the application. Used in cases where a specific make/model is required to run the application.
     * @var array|string|DefinedTerm|null
     */
    public null|array|string|DefinedTerm $availableOnDevice;

    /**
     * Countries for which the application is not supported. You can also provide the two-letter ISO 3166-1 alpha-2 country code.
     * @var array|string|Country|null
     */
    public null|array|string|Country $countriesNotSupported ;

    /**
     * Countries for which the application is supported. You can also provide the two-letter ISO 3166-1 alpha-2 country code.
     * @var array|string|Country|null
     */
    public null|array|string|Country $countriesSupported ;

    /**
     * If the file can be downloaded, URL to download the binary.
     * @var array|string|null
     */
    public null|array|string $downloadUrl;

    /**
     * Features or modules provided by this application (and possibly required by other applications).
     * @var array|string|null|object
     */
    public null|array|string|object $featureList ;

    /**
     * Size of the application / package (e.g. 18MB). In the absence of a unit (MB, KB etc.), KB will be assumed.
     * @var string|null
     */
    public ?string $fileSize;

    /**
     * URL at which the app may be installed, if different from the URL of the item.
     * @var array|string|null
     */
    public null|array|string $installUrl;

    /**
     * Minimum memory requirements.
     * @var string|null
     */
    public ?string $memoryRequirements ;

    /**
     * Operating systems supported (Windows 7, OS X 10.6, Android 1.6).
     * @var null|array|string|object
     */
    public null|array|string|object $operatingSystem ;

    /**
     * Permission(s) required to run the app (for example, a mobile app may require full internet access or may run only on wifi).
     * @var null|array|string|object
     */
    public null|array|string|object $permissions ;

    /**
     * Processor architecture required to run the application (e.g. IA64).
     * @var null|string|object
     */
    public null|string|object $processorRequirements ;

    /**
     * Description of what changed in this version.
     * @var null|string|object
     */
    public null|string|object $releaseNotes ;

    /**
     * A link to a screenshot image of the app.
     * @var array|string|ImageObject|null
     */
    public null|array|string|ImageObject $screenshot ;

    /**
     * Additional content for a software application.
     * @var array|SoftwareApplication|null
     */
    public null|array|SoftwareApplication $softwareAddOn ;

    /**
     * Software application help.
     * @var CreativeWork|null
     */
    public null|CreativeWork $softwareHelp ;

    /**
     * Component dependency requirements for application.
     * This includes runtime environments and shared libraries that are not included in the application distribution package, but required to run the application (examples: DirectX, Java or .NET runtime).
     * @var null|string|object
     */
    public null|string|object $softwareRequirements ;

    /**
     * Version of the software instance.
     * @var ?string
     */
    public ?string $softwareVersion ;

    /**
     * Storage requirements (free space required).
     * @var null|string|object
     */
    public null|string|object $storageRequirements ;

    /**
     * Supporting data for a SoftwareApplication.
     * @var DataFeed|null
     */
    public ?DataFeed $supportingData ;
}