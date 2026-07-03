<?php

namespace xyz\oihana\schema\organizations;

use oihana\reflect\attributes\HydrateWith;

use org\schema\creativeWork\WebSite;
use org\schema\DefinedTerm;
use org\schema\enumerations\DeliveryMethod;
use org\schema\organizations\Corporation ;
use org\schema\PropertyValue;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\TaxRate;
use xyz\oihana\schema\traits\SetContactPointTrait;
use xyz\oihana\schema\traits\SetPostalAddressTrait;

/**
 * A specialized business entity representation.
 *
 * This class extends the Schema.org 'Corporation' model to provide a bridge between
 * official structured data standards and internal company logic.
 * It serves as the base entity for managing establishments, affiliates, and
 * corporate structures within the grouping.
 *
 * Key features:
 * - Compliance with Schema.org/Corporation for SEO and interoperability.
 * - Native support for French administrative identifiers (SIRET via taxID, APE via naics/ape).
 * - Integrated hydration logic for postal addresses and contact points via traits.
 * - Custom status and industry tracking for ERP synchronization.
 *
 * @see https://schema.org/Corporation
 * @see https://schema.org/Organization
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\organizations
 * @since   1.3.0
 */
class Company extends Corporation
{
    use SetContactPointTrait  ,
        SetPostalAddressTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * A property-value pair representing an additional characteristic of the entity,
     * e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     */
    #[HydrateWith( PropertyValue::class ) ]
    public null|array|PropertyValue $additionalProperty = null ;

    /**
     * A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     * @var null|array|string|DefinedTerm
     */
    public null|array|string|DefinedTerm $category ;

    /**
     * The delivery method of the customer.
     * @var null|array|string|DeliveryMethod|DefinedTerm
     */
    public null|array|string|DeliveryMethod|DefinedTerm $deliveryMethod ;

    /**
     * A minimum order cost above (or at) which the shipping rate becomes free.
     * @var null|float|int
     */
    public null|float|int $freeShippingThreshold ;

    /**
     * The industry associated with the job position or the organization.
     * @var array|string|DefinedTerm|null
     */
    public null|array|string|DefinedTerm $industry ;

    /**
     * The type of invoice for the customer.
     * @var null|array|string|DefinedTerm
     */
    public null|array|string|DefinedTerm $invoiceType ;

    /**
     * The status of the customer.
     * @var int|null
     */
    public ?int $status ;

    /**
     * The default taxe rate information of the company.
     * @var array|TaxRate|string|int|null
     */
    public array|TaxRate|string|int|null $vat ;

    /**
     * The website(s) of the company.
     * @var array|string|WebSite|null
     */
    #[HydrateWith( WebSite::class ) ]
    public null|array|string|WebSite $website ;

    /**
     * Invoked when writing data to inaccessible (protected or private) or non-existing properties.
     *
     * @param string $property Property name
     *
     * @param mixed $value Value of the property.
     *
     * @return void
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setCompanyProperties( $property , $value ) ;
    }

    /**
     * Set the custom company properties.
     *
     * @param string $property Property name
     * @param mixed $value Value of the property.
     *
     * @return bool
     */
    public function setCompanyProperties( string $property , mixed $value ) :bool
    {
        return $this->setPostalAddressProperties ( $property , $value ) ||
               $this->setContactPointProperty    ( $property , $value ) ;
    }
}