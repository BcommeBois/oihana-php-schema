<?php

namespace org\schema;

use org\schema\traits\MeasurementTechniqueTrait;
use org\schema\traits\ValueTrait;

class PropertyValue extends StructuredValue
{
    use MeasurementTechniqueTrait ,
        ValueTrait ;

    /**
     * A commonly used identifier for the characteristic represented by the property, e.g. a manufacturer or a standard code for a property.
     * @var string
     */
    public mixed $propertyID ;
}