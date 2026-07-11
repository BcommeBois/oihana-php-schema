<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The enumeration of all the pricing condition properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.4.0
 */
trait PricingCondition
{
    public const string ADDITIONAL_PROPERTY = 'additionalProperty' ;
    public const string ADJUSTMENT          = 'adjustment' ;
    public const string EXCLUDED_CUSTOMERS  = 'excludedCustomers' ;
    public const string EXCLUDED_PRODUCTS   = 'excludedProducts' ;
    public const string FIXED_PRICE         = 'fixedPrice' ;
    public const string FREE                = 'free' ;
    public const string QUANTITY_DISCOUNT   = 'quantityDiscount' ;
    public const string SELECTOR            = 'selector' ;
    public const string SUBSTITUTES_SEGMENT = 'substitutesSegment' ;
    public const string VALID_FROM          = 'validFrom' ;
    public const string VALID_THROUGH       = 'validThrough' ;
}
