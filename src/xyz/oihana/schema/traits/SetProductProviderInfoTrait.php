<?php

namespace xyz\oihana\schema\traits;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ProductProviderInfo;

/**
 * Trait to manage product provider information for an entity.
 *
 * This trait provides methods to safely set properties on a `ProductProviderInfo` object.
 * It ensures that the `productInfo` object is initialized before setting any values.
 *
 * @property null|ProductProviderInfo $productInfo The associated product provider information object.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait SetProductProviderInfoTrait
{
    /**
     * Internal method to set a single property on the `ProductProviderInfo` object.
     *
     * If the `productInfo` property is not yet initialized, it will be created automatically.
     * If the value is `null`, the property will be cleared.
     *
     * @param string $name  The property name to set on `ProductProviderInfo`.
     * @param mixed  $value The value to assign to the property.
     *
     * @return bool True if the property was successfully set or cleared, false otherwise.
     */
    protected function setProductProviderInfoProperty( string $name , mixed $value ):bool
    {
        if( isset( $value ) )
        {
            if( !$this->productInfo instanceof ProductProviderInfo )
            {
                $this->productInfo = new ProductProviderInfo() ;
                if( isset( $this->id ) )
                {
                    $this->productInfo->id = $this->id ;
                }
            }
            $this->productInfo->{ $name } = $value ;
            return true ;
        }
        else if( $this->productInfo instanceof ProductProviderInfo )
        {
            $this->productInfo->{ $name } = null ;
            return true ;
        }
        return false ;
    }

    /**
     * Set one of the predefined product provider properties.
     *
     * This method maps certain standardized property names (from `Prop`) to the
     * internal `productInfo` object.
     *
     * Supported properties include:
     * - `BUYING_PRICE`
     * - `BUYING_PRICE_MARGIN`
     * - `BUYING_PRICE_REFERENCE_QUANTITY`
     * - `BUYING_PRICE_REFERENCE_QUANTITY_UNIT_CODE`
     * - `HAS_QUANTITY_DISCOUNT`
     * - `NEXT_BUYING_PRICE`
     * - `NEXT_BUYING_PRICE_DATE`
     * - `PRIMARY`
     *
     * @param string $property The property name to set.
     * @param mixed  $value    The value to assign.
     *
     * @return bool True if the property was handled, false otherwise.
     */
    public function setProductProviderInfoProperties( string $property , mixed $value ):bool
    {
        return match( $property )
        {
            Oihana::BUYING_PRICE ,
            Oihana::BUYING_PRICE_MARGIN ,
            Oihana::BUYING_PRICE_REFERENCE_QUANTITY ,
            Oihana::BUYING_PRICE_REFERENCE_QUANTITY_UNIT_CODE ,
            Oihana::HAS_QUANTITY_DISCOUNT ,
            Oihana::NEXT_BUYING_PRICE ,
            Oihana::NEXT_BUYING_PRICE_DATE ,
            Oihana::PRIMARY => $this->setProductProviderInfoProperty( $property , $value ) ,
            default       => false,
        };
    }
}