<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The product's providers information properties enumeration.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.3.0
 */
trait ProductProviderInfo
{
    public const string BUYING_PRICE                              = 'buyingPrice' ;
    public const string BUYING_PRICE_MARGIN                       = 'buyingPriceMargin' ;
    public const string BUYING_PRICE_REFERENCE_QUANTITY           = 'buyingPriceReferenceQuantity' ;
    public const string BUYING_PRICE_REFERENCE_QUANTITY_UNIT_CODE = 'buyingPriceReferenceUnitCode' ;
    public const string HAS_DISCOUNTS                             = 'hasDiscounts' ;
    public const string HAS_QUANTITY_DISCOUNT                     = 'hasQuantityDiscount' ;
    public const string NEXT_BUYING_PRICE                         = 'nextBuyingPrice';
    public const string NEXT_BUYING_PRICE_DATE                    = 'nextBuyingPriceDate';
    public const string PRICE_PER_PURCHASE_UNIT                   = 'pricePerPurchaseUnit';
    public const string PRIMARY                                   = 'primary';
}