<?php

namespace org\schema;

/**
 * The measurement definition.
 */
class Measurement extends Thing
{
    /**
     * The aspect of an object being measured.
     * Examples : height, width, depth, weight, volume, circumference, etc.
     */
    public ?float $dimension ;

    /**
     * The unit of measurement used when measuring a dimension.
     * Examples : cm, metres, inches, g
     */
    public ?string $unit ;

    /**
     * The numeric value of the Measurement of a dimension.
     * Examples : 23, 14.5
     */
    public float|string|null $value ;
}


