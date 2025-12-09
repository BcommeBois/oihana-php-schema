<?php

namespace org\schema\traits;

trait ValueTrait
{
    /**
     * The upper value of some characteristic or property.
     * @var null|float|int
     */
    public null|float|int $maxValue ;

    /**
     * The lower value of some characteristic or property.
     * @var null|float|int
     */
    public null|float|int $minValue ;

    /**
     * The unit of measurement given using the UN/CEFACT Common Code (3 characters) or a URL.
     * Other codes than the UN/CEFACT Common Code may be used with a prefix followed by a colon.
     * @var null|string
     */
    public ?string $unitCode ;

    /**
     * A string or text indicating the unit of measurement.
     * Useful if you cannot provide a standard unit code for unitCode.
     * @var null|string
     */
    public ?string $unitText ;

    /**
     * The value of a QuantitativeValue or property value node.
     * @var mixed
     */
    public mixed $value ;

    /**
     * The lower value of some characteristic or property.
     * @var mixed
     */
    public mixed $valueReference ;
}