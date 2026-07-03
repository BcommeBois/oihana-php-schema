<?php

namespace xyz\oihana\schema\traits;

use org\schema\constants\Schema;
use org\schema\UnitPriceSpecification;

use function oihana\core\date\isDate;

/**
 * Helpers to inspect a collection of {@see \org\schema\UnitPriceSpecification} objects.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait UnitPriceSpecificationTrait
{
    /**
     * Retrieves the latest UnitPriceSpecification based on the given property name.
     *
     * Iterates through the provided specifications to identify the UnitPriceSpecification
     * with the most recent date based on the specified property. If no valid date is found
     * in the specifications, or if the input array is null, the method returns null.
     *
     * @param array<UnitPriceSpecification>|null $specifications Array of specifications to be evaluated.
     * @param string $propertyName The property used to extract the date for comparison. Defaults to Schema::VALID_FROM.
     * @param string $format The date format (default 'Y-m-d').
     * @return UnitPriceSpecification|null The latest UnitPriceSpecification or null if not found.
     */
    public function getLastUnitPriceSpecification
    (
        ?array $specifications ,
        string $propertyName = Schema::VALID_FROM ,
        string $format       = 'Y-m-d'
    )
    :?UnitPriceSpecification
    {
        $currentDate = null ;
        $last = null ;
        if( is_array( $specifications ) )
        {
            foreach ( $specifications as $specification )
            {
                if( $specification instanceof UnitPriceSpecification )
                {
                    $date = $specification->{ $propertyName } ?? null ;
                    if ( isDate( $date , $format ) )
                    {
                        if ( $currentDate === null || strtotime( $date ) > strtotime( $currentDate ) )
                        {
                            $currentDate = $date ;
                            $last = $specification ;
                        }
                    }
                }
            }
        }
        return $last ;
    }
}