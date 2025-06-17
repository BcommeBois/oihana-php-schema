<?php

namespace org\schema\traits\helpers ;

use org\schema\PropertyValue;

trait SetAdditionalPropertyTrait
{
    /**
     * Internal method to inject custom properties in the PostalAddress property of the place.
     * @param array $init
     * @return void
     */
    protected function setAdditionalProperty( array $init ):void
    {
        if( !is_array( $this->additionalProperty ) )
        {
            $this->additionalProperty = [] ;
        }
        $this->additionalProperty[] = new PropertyValue( $init ) ;
    }
}