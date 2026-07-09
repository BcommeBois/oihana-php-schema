<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The enumeration of all the pricing condition selector properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.4.0
 */
trait PricingConditionSelector
{
    public const string AREA_SERVED    = 'areaServed' ;
    public const string CATEGORY_LEVEL = 'categoryLevel' ;
    public const string CUSTOMER_ID    = 'customerId' ;
    public const string CUSTOMER_SCOPE = 'customerScope' ;
    public const string ITEM_ID        = 'itemId' ;
    public const string ITEM_SCOPE     = 'itemScope' ;
    public const string PROVIDER_ID    = 'providerId' ;
}
