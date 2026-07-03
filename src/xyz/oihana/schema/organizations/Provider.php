<?php

namespace xyz\oihana\schema\organizations ;

use org\schema\DefinedTerm;
use org\schema\Organization;

use xyz\oihana\schema\products\ProductProviderInfo;
use xyz\oihana\schema\traits\SetProductProviderInfoTrait;

/**
 * A provider representation.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\organizations
 * @since   1.3.0
 */
class Provider extends Company
{
    use SetProductProviderInfoTrait ;

    /**
     * The amount carriage paid of the provider.
     * @var int|float|null
     */
    public null|int|float $amountCarriagePaid ;

    /**
     * The carrier of the provider.
     * @var null|array|string|Organization|Company|Provider
     */
    public null|array|string|Organization|Company|Provider $carrier ;

    /**
     * Indicates if the provider accept an Acknowledgment Of Receipt.
     * @var ?bool
     */
    public ?bool $hasAcknowledgmentOfReceipt ;

    /**
     * The minimum order value accepted by the provider.
     * @var float|int|null
     */
    public null|float|int $minimumOrderValue ;

    /**
     * The product information of the provider.
     * @var ProductProviderInfo|null
     */
    public ?ProductProviderInfo $productInfo = null ;

    /**
     * Provider functional type.
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $providerType ;

    /**
     * The share capital of the provider.
     * @var float|int|null
     */
    public null|float|int $shareCapital ;

    /**
     * The shipping delivery time of the provider.
     * @var int|null
     */
    public null|int $shippingDeliveryTime ;

    /**
     * Indicates if the provider accept a valued order.
     * @var ?bool
     */
    public ?bool $valuedOrder ;

    /**
     * Invoked when writing data to inaccessible (protected or private) or non-existing properties.
     * @param string $property Property name
     * @param mixed $value Value of the property.
     * @return void
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setCompanyProperties             ( $property , $value ) ||
        $this->setProductProviderInfoProperties ( $property , $value ) ;
    }
}