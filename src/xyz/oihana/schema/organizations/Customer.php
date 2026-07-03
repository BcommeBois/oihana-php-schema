<?php

namespace xyz\oihana\schema\organizations;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\Person;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;
use xyz\oihana\schema\constants\CustomerAdditionalProperty;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\PriceSegmentation;

/**
 * A customer representation.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\organizations
 * @since   1.3.0
 */
class Customer extends Company
{
    use SetAdditionalPropertyTrait ;

    /**
     * The companies assigned to the customer.
     * @var string|array|Organization|null
     */
    public null|string|array|Organization $assignedCompany ;

    /**
     * The point of sale assigned to the customer (warehouse, shop, etc.).
     * @var null|int|string|array|Warehouse
     */
    public null|int|string|array|Warehouse $assignedPOS ;

    /**
     * The seller(s) assigned to the customer.
     * @var string|int|array|Person|null
     */
    public null|int|string|array|Person $assignedSeller ;

    /**
     * The credit status of the customer.
     * @var array|string|DefinedTerm|null
     */
    public null|array|string|DefinedTerm $creditStatus ;

    /**
     * The payment terms of the customer.
     * @var string|array|DefinedTerm|null
     */
    public null|string|array|DefinedTerm $paymentTerms ;

    /**
     * The default price segmentation of the customer.
     * @var null|int|string|array|PriceSegmentation
     */
    public null|int|string|array|PriceSegmentation $priceSegmentation ;

    /**
     * The unloading method of the customer.
     * @var null|array|string|DefinedTerm
     */
    public null|array|string|DefinedTerm $unloadingMethod ;


    /**
     * @param string $property Property name
     * @param mixed $value Value of the property.
     * @return void
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setAdditionalProperties ( $property , $value ) ||
        $this->setCompanyProperties    ( $property , $value ) ;
    }

    /**
     * Set a new optional additional properties of the customer.
     * The method is invoked in the magic {@see Customer::_set} method.
     *
     * @param string $property Property name
     * @param mixed  $value    Value of the property.
     *
     * @return bool True if the property was handled, false otherwise
     */
    public function setAdditionalProperties( string $property , mixed $value ) :bool
    {
        if( CustomerAdditionalProperty::includes( $property ) && isset( $value ) && is_string( $value ) )
        {
            $this->setAdditionalProperty
            ([
                Schema::PROPERTY_ID => $property ,
                Schema::VALUE       => CustomerAdditionalProperty::normalize( $property , $value )
            ]) ;
            return true;
        }
        return false ;
    }
}