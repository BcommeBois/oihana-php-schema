<?php

namespace org\schema\constants\properties;

/**
 * The OpeningHoursSpecification properties enumeration.
 */
trait CompoundPriceSpecification
{
    use PriceSpecification ;

    const string PRICE_COMPONENT = 'priceComponent' ;
    const string PRICE_TYPE      = 'priceType' ;
}


