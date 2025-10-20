<?php

namespace org\schema\traits;

/**
 * The DCMI Metadata Terms trait.
 *
 * @see https://www.dublincore.org/specifications/dublin-core/dcmi-terms/
 */
trait DublinCoreTrait
{
    /**
     * A summary of the resource.
     * @var string|null
     */
    public ?string $abstract ;

    /**
     * Information about who access the resource or an indication of its security status.
     * @var string|null
     */
    public ?string $accessRight ;

    /**
     * The method by which items are added to a collection.
     * @var string|null
     */
    public ?string $accrualMethod ;

    /**
     * The frequency with which items are added to a collection.
     * @var string|null
     */
    public ?string $accrualPeriodicityMore ;

    /**
     * The policy governing the addition of items to a collection.
     * @var string|null
     */
    public ?string $accrualPolicy ;

    /**
     * An alternative name for the resource.
     * @var string|null
     */
    public ?string $alternative ;

    /**
     * Date that the resource became or will become available.
     * @var string|null
     */
    public ?string $available ;

    /**
     * A bibliographic reference for the resource.
     */
    public ?string $bibliographicCitation ;

    /**
     * An established standard to which the described resource conforms.
     * @var string|null
     */
    public ?string $conformsTo ;

    /**
     * An entity responsible for making contributions to the resource.
     * @var string|null
     */
    public ?string $contributor ;

    /**
     * The spatial or temporal topic of the resource, spatial applicability of the resource, or jurisdiction under which the resource is relevant.
     * @var string|null
     */
    public ?string $coverage ;

    /**
     * Date of creation of the resource.
     *
     * Recommended practice is to describe the date, date/time, or period of time as recommended for the property Date, of which this is a subproperty.
     *
     * @var string|null
     */
    public ?string $created ;

    /**
     * An entity responsible for making the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $creator ;

    /**
     * A point or period of time associated with an event in the lifecycle of the resource.
     * @var string|null
     */
    public ?string $date ;

    /**
     * Date of acceptance of the resource.
     * @var string|null
     */
    public ?string $dateAccepted ;

    /**
     * Date of copyright of the resource.
     * @var string|null
     */
    public ?string $dateCopyrighted ;

    /**
     * Date of submission of the resource.
     * @var string|null
     */
    public ?string $dateSubmitted ;

    /**
     * An account of the resource.
     * @var string|null
     */
    public ?string $description ;

    /**
     * Audience Education Level
     * @var string|null
     */
    public ?string $educationLevel ;

    /**
     * The size or duration of the resource.
     *
     * Recommended practice is to specify the file size in megabytes and duration in ISO 8601 format
     *
     * @var string|null
     */
    public ?string $extent ;

    /**
     * The file format, physical medium, or dimensions of the resource.
     * @var string|null
     */
    public ?string $format ;

    /**
     * Has Format
     * @var string|array|object|null
     */
    public string|array|object|null $hasFormat ;

    /**
     * A related resource that is a version, edition, or adaptation of the described resource.
     * @var string|array|object|null
     */
    public string|array|object|null $hasVersion ;

    /**
     * An unambiguous reference to the resource within a given context.
     * @var string|null
     */
    public string|null $identifier ;

    /**
     * A process, used to engender knowledge, attitudes and skills, that the described resource is designed to support.
     * @var string|object|null
     */
    public string|object|null $instructionalMethodMore ;

    /**
     * A pre-existing related resource that is substantially the same as the described resource, but in another format.
     * @var string|object|null
     */
    public string|object|null $isFormatOf ;

    /**
     * A related resource in which the described resource is physically or logically included.
     * @var string|object|null
     */
    public string|object|null $isPartOf ;

    /**
     * A related resource that references, cites, or otherwise points to the described resource.
     * @var string|object|null
     */
    public string|object|null $isReferencedBy ;

    /**
     * A related resource that supplants, displaces, or supersedes the described resource.
     * @var string|object|null
     */
    public string|object|null $isReplacedBy ;

    /**
     * A related resource that requires the described resource to support its function, delivery, or coherence.
     * @var string|object|array|null
     */
    public string|object|array|null $isRequiredBy ;

    /**
     * Date of formal issuance of the resource.
     *
     * Recommended practice is to describe the date, date/time, or period of time as recommended for the property Date, of which this is a subproperty.
     *
     * @var string|null
     */
    public ?string $issued ;

    /**
     * A related resource of which the described resource is a version, edition, or adaptation.
     * @var string|object|null
     */
    public string|object|null $isVersionOf ;

    /**
     * A language of the resource.
     * @var string|array|object|null
     */
    public string|array|object|null $language ;

    /**
     * A legal document giving official permission to do something with the resource.
     * @var string|null
     */
    public string|null $license ;

    /**
     * An entity that mediates access to the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $mediator ;

    /**
     * The material or physical carrier of the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $medium ;

    /**
     * Date on which the resource was changed.
     *
     * Recommended practice is to describe the date, date/time, or period of time as recommended for the property Date, of which this is a subproperty.
     *
     * @var string|null
     */
    public ?string $modified ;

    /**
     * A statement of any changes in ownership and custody of the resource since its creation that are significant for its authenticity, integrity, and interpretation.
     *
     * @var string|array|object|null
     */
    public string|array|object|null $provenance ;

    /**
     * An entity responsible for making the resource available.
     * @var string|object|array|null
     */
    public string|object|array|null $publisher ;

    /**
     * A related resource.
     *
     * This property is intended to be used with non-literal values.
     * This property is an inverse property of Is Replaced By.
     *
     * @var string|object|array|null
     */
    public string|object|array|null $relation ;

    /**
     * A related resource that is referenced, cited, or otherwise pointed to by the described resource.
     *
     * This property is intended to be used with non-literal values.
     * This property is an inverse property of Is Referenced By.
     *
     * @var string|object|array|null
     */
    public string|object|array|null $references ;

    /**
     * A related resource that is supplanted, displaced, or superseded by the described resource.
     *
     * Recommended practice is to identify the related resource by means of a URI.
     * If this is not possible or feasible, a string conforming to a formal identification system may be provided.
     *
     * @var string|object|array|null
     */
    public string|object|array|null $replaces ;

    /**
     * A related resource that is required by the described resource to support its function, delivery, or coherence.
     *
     * This property is intended to be used with non-literal values.
     * This property is an inverse property of Is Required By.
     *
     * @var string|object|array|null
     */
    public string|object|array|null $requires ;

    /**
     * Information about rights held in and over the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $rights ;

    /**
     * A person or organization owning or managing rights over the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $rightsHolder ;

    /**
     * A related resource from which the described resource is derived.
     * @var string|object|array|null
     */
    public string|object|array|null $source ;

    /**
     * Spatial characteristics of the resource.
     * @var string|object|array|null
     */
    public string|object|array|null $spatial ;

    /**
     * A topic of the resource.
     *
     * Recommended practice is to refer to the subject with a URI. If this is not possible or feasible,
     * a literal value that identifies the subject may be provided.
     *
     * Both should preferably refer to a subject in a controlled vocabulary.
     *
     * @var string|object|array|null
     */
    public string|object|array|null $subject ;

    /**
     * A list of subunits of the resource.
     *
     * @var string|array|object|null
     */
    public string|array|object|null $tableOfContents ;

    /**
     * Temporal characteristics of the resource.
     *
     * @var string|array|object|null
     */
    public string|array|object|null $temporal ;

    /**
     * The title of the ressource.
     * @var string|object|array|null
     */
    public string|object|array|null $title ;

    /**
     * The nature or genre of the resource.
     * @var string|object|null
     */
    public string|object|null $type ;

    /**
     * Date (often a range) of validity of a resource.
     *
     * Recommended practice is to describe the date, date/time, or period of time as recommended for the property Date, of which this is a subproperty.
     *
     * @var string|null
     */
    public ?string $valid ;
}


