<?php

namespace org\schema\traits\helpers ;

use org\schema\constants\Schema;

/**
 * Reads the values stored in the `additionalProperty` list of a schema.org thing.
 *
 * The counterpart of {@see SetAdditionalPropertyTrait}, which writes them. A thing
 * carries these entries as a flat list of `PropertyValue`, each naming a
 * `propertyID` and its `value` — the list is the only way to know whether a
 * contact receives the quotes, or whether an address is the billing one.
 *
 * Both shapes are honoured: the hydrated objects a schema builds, and the plain
 * arrays a document comes back as when nothing re-hydrated it.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package org\schema\traits\helpers
 * @since   1.4.0
 */
trait GetAdditionalPropertyTrait
{
    /**
     * The value stored under the given property id, or null when absent.
     *
     * @param string $propertyID The `propertyID` to look for.
     *
     * @return mixed The stored value, or null when the thing carries no such entry.
     */
    public function getAdditionalPropertyValue( string $propertyID ) :mixed
    {
        $properties = $this->additionalProperty ?? null ;

        if ( !is_array( $properties ) )
        {
            return null ;
        }

        foreach ( $properties as $property )
        {
            if ( is_array( $property ) )
            {
                if ( ( $property[ Schema::PROPERTY_ID ] ?? null ) === $propertyID )
                {
                    return $property[ Schema::VALUE ] ?? null ;
                }

                continue ;
            }

            if ( is_object( $property ) && ( $property->propertyID ?? null ) === $propertyID )
            {
                return $property->value ?? null ;
            }
        }

        return null ;
    }

    /**
     * Whether the given property id is stored as a true flag.
     *
     * Tolerant on purpose: the value may come back as a real boolean, as the
     * string `"1"`, or as the number `1`, depending on how the thing was built.
     * An absent entry is false — nothing was claimed.
     *
     * @param string $propertyID The `propertyID` to look for.
     *
     * @return bool
     */
    public function hasAdditionalPropertyFlag( string $propertyID ) :bool
    {
        return filter_var( $this->getAdditionalPropertyValue( $propertyID ) , FILTER_VALIDATE_BOOLEAN ) ;
    }
}