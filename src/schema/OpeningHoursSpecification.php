<?php

namespace org\schema;

/**
 * A structured value providing information about the opening hours of a place or a certain service inside a place.
 */
class OpeningHoursSpecification extends StructuredValue
{
    /**
     * The closing hour of the place or service on the given day(s) of the week (Time).
     */
    public array|string|null $closes ;

    /**
     * The day of the week for which these opening hours are valid (DayOfWeek).
     */
    public array|string|null $dayOfWeek ;

    /**
     * The opening hour of the place or service on the given day(s) of the week (Time).
     */
    public array|string|null $opens ;

    /**
     * The date when the item becomes valid (DateTime).
     */
    public string $validFrom ;

    /**
     * The end of the validity of offer, price specification, or opening hours data (DateTime).
     */
    public string $validThrough ;
}


