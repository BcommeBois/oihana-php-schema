<?php

namespace org\schema;

/**
 * A predefined value for a product characteristic, e.g. the power cord plug type 'US' or the garment sizes 'S', 'M', 'L', and 'XL'.
 */
class QualitativeValue extends Thing
{
    /**
     * A property-value pair representing an additional characteristic of the entity,
     * e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     * @var array|null|PropertyValue
     */
    public array|null|PropertyValue $additionalProperty ;

    /**
     * This ordering relation for qualitative values indicates that the subject is equal to the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $equal ;

    /**
     * This ordering relation for qualitative values indicates that the subject is greater than the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $greater ;

    /**
     * This ordering relation for qualitative values indicates that the subject is greater than or equal to the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $greaterOrEqual ;

    /**
     * This ordering relation for qualitative values indicates that the subject is lesser than the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $lesser ;

    /**
     * This ordering relation for qualitative values indicates that the subject is lesser than or equal to the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $lesserOrEqual ;

    /**
     * This ordering relation for qualitative values indicates that the subject is not equal to the object.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $nonEqual ;

    /**
     * The lower value of some characteristic or property.
     * @var mixed
     */
    public mixed $valueReference ;
}