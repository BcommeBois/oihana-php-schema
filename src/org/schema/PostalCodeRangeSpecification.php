<?php

namespace org\schema;

/**
 * Indicates a range of postal codes, usually defined as the set of valid codes
 * between postalCodeBegin and postalCodeEnd, inclusively.
 */
class PostalCodeRangeSpecification extends StructuredValue
{
    /**
     * First postal code in a range (included).
     * @var null|string
     */
    public ?string $postalCodeBegin ;

    /**
     * Last postal code in the range (included). Needs to be after postalCodeBegin.
     * @var null|string
     */
    public ?string $postalCodeEnd ;
}