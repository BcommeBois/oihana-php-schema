<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\products\ExtraPriceSpecification;
use xyz\oihana\schema\constants\traits\products\PaymentCondition;
use xyz\oihana\schema\constants\traits\products\PaymentMethod;
use xyz\oihana\schema\constants\traits\products\PriceQuantityDiscount;
use xyz\oihana\schema\constants\traits\products\PriceSegmentation;
use xyz\oihana\schema\constants\traits\products\PriceSegmentationFactor;
use xyz\oihana\schema\constants\traits\products\PricingCondition;
use xyz\oihana\schema\constants\traits\products\PricingConditionSelector;
use xyz\oihana\schema\constants\traits\products\ProductAdditionalProperty;
use xyz\oihana\schema\constants\traits\products\ProductProviderInfo;
use xyz\oihana\schema\constants\traits\products\ProductType;
use xyz\oihana\schema\constants\traits\products\ProductWarehouseInfo;
use xyz\oihana\schema\constants\traits\products\ProviderProductWarehouseInfo;
use xyz\oihana\schema\constants\traits\products\StockLevel;

/**
 * The enumeration of all products properties constants.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait ProductsTrait
{
    use ExtraPriceSpecification      ,
        PaymentCondition             ,
        PaymentMethod                ,
        PriceQuantityDiscount        ,
        PriceSegmentation            ,
        PriceSegmentationFactor      ,
        PricingCondition             ,
        PricingConditionSelector     ,
        ProductAdditionalProperty    ,
        ProductProviderInfo          ,
        ProductType                  ,
        ProductWarehouseInfo         ,
        ProviderProductWarehouseInfo ,
        StockLevel                   ;
}