<?php

namespace org\schema\constants\traits;

/**
 * The value properties enumeration.
 *
 * The measurement terms a PropertyValue also carries are named by
 * {@see MeasurementTechnique}.
 */
trait Values
{
    const string ADDITIONAL_PROPERTY   = 'additionalProperty' ;
    const string EQUAL                 = 'equal' ;
    const string GREATER               = 'greater' ;
    const string GREATER_OR_EQUAL      = 'greaterOrEqual' ;
    const string LESSER                = 'lesser' ;
    const string LESSER_OR_EQUAL       = 'lesserOrEqual' ;
    const string NOT_EQUAL             = 'notEqual' ;
    const string MAX_VALUE             = 'maxValue' ;
    const string MIN_VALUE             = 'minValue' ;
    const string PROPERTY_ID           = 'propertyID' ;
    const string UNIT_CODE             = 'unitCode' ;
    const string UNIT_TEXT             = 'unitText' ;
    const string VALUE                 = 'value'    ;
    const string VALUE_REFERENCE       = 'valueReference' ;
}