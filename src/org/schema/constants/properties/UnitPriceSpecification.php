<?php

namespace org\schema\constants\properties;

trait UnitPriceSpecification
{
    use PriceSpecification ;

    const string BILLING_DURATION     = 'billingDuration' ;
    const string BILLING_INCREMENT    = 'billingIncrement' ;
    const string BILLING_START        = 'billingStart' ;
    const string PRICE_COMPONENT_TYPE = 'priceComponentType' ;
    const string PRICE_TYPE           = 'priceType' ;
    const string REFERENCE_QUANTITY   = 'referenceQuantity' ;
    const string UNIT_CODE            = 'unitCode' ;
    const string UNIT_TEXT            = 'unitText' ;
}


