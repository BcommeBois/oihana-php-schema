<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The enumeration of all the product additional properties constants,
 * including the magic properties used to define the eligible quantities.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.3.0
 */
trait ProductAdditionalProperty
{
    public const string CATEGORY          = 'category'           ;
    public const string DENSITY           = 'density'            ;
    public const string ELIGIBLE_QUANTITY = 'eligibleQuantity'   ;
    public const string HEIGHT            = 'height'             ;
    public const string IN_STOCK          = 'inStock'            ;
    public const string LENGTH            = 'length'             ;
    public const string PRICE_CATEGORY    = 'priceCategory'      ;
    public const string PRODUCT_TYPE      = 'productType'        ;
    public const string STATUS            = 'status'             ;
    public const string UNIT_OF_SALE      = 'unitOfSale'         ;
    public const string VAT               = 'vat'                ;
    public const string VOLUME            = 'volume'             ;
    public const string WEB_CATEGORY      = 'webCategory'        ;
    public const string WIDTH             = 'width'              ;
    public const string WEIGHT            = 'weight'             ;

    // ---- Magic Properties used to defines the eligibleQuantity property

    public const string ELIGIBLE_PACKAGE_QUANTITY_VALUE = 'eligiblePackageQuantityValue' ;
    public const string ELIGIBLE_PACKAGE_QUANTITY_CODE  = 'eligiblePackageQuantityCode'  ;
    public const string ELIGIBLE_PALLET_QUANTITY_VALUE  = 'eligiblePalletQuantityValue'  ;
    public const string ELIGIBLE_PALLET_QUANTITY_CODE   = 'eligiblePalletQuantityCode'   ;
    public const string ELIGIBLE_UNIT_QUANTITY_CODE     = 'eligibleUnitQuantityCode'     ;

}