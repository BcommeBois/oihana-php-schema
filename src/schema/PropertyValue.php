<?php

namespace org\schema;

use org\schema\traits\ValueTrait;

class PropertyValue extends StructuredValue
{
    use ValueTrait ;

    /**
     * A sub property of measurementTechnique that can be used for specifying specific methods, in particular via MeasurementMethodEnum.
     * @var mixed
     */
    public mixed $measurementMethod ;

    /**
     * A technique, method or technology.
     * @var mixed
     */
    public mixed $measurementTechnique ;

    /**
     * A commonly used identifier for the characteristic represented by the property, e.g. a manufacturer or a standard code for a property.
     * @var string
     */
    public mixed $propertyID ;
}