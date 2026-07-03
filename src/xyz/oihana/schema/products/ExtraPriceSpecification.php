<?php

namespace xyz\oihana\schema\products;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;

use ReflectionException;

/**
 * Represents a specific factor associated with price segmentation, extending the UnitPriceSpecification functionality.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ExtraPriceSpecification extends UnitPriceSpecification
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The extra segmentations factor associated with the price segmentation.
     * @var null|string|array
     */
    public null|string|array $extras ;

    /**
     * Simplify the price segmentation factor to a basic UnitPriceSpecification.
     * Removes the 'extras' property.
     * @param ?int $price The optional price to inject in the new definition.
     * @return UnitPriceSpecification
     * @throws ReflectionException
     */
    public function toUnitPriceSpecification( ?int $price = null ) : UnitPriceSpecification
    {
        $priceSpecification = new UnitPriceSpecification();
        $properties = $this->getPublicProperties( $priceSpecification ) ;

        foreach( $properties as $property )
        {
            $name = $property->getName();
            if( isset( $this->{ $name } ) )
            {
                $priceSpecification->{ $name } = $this->{ $name } ;
            }
        }

        $price = $price ?? $this->price ?? null ;
        if( $price > 0 )
        {
            $priceSpecification->price = $price ;
        }

        return $priceSpecification ;
    }
}