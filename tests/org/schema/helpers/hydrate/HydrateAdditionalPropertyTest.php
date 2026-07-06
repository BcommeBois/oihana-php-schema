<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\PropertyValue;

use function org\schema\helpers\hydrate\hydrateAdditionalProperty;

final class HydrateAdditionalPropertyTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayOfProperties(): void
    {
        $properties = hydrateAdditionalProperty(
        [
            [ 'propertyID' => 'grain'  , 'value' => true ] ,
            [ 'propertyID' => 'length' , 'value' => 250  ] ,
        ]) ;

        $this->assertIsArray( $properties ) ;
        $this->assertCount( 2 , $properties ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $properties ) ;
        $this->assertSame( 'grain' , $properties[0]->propertyID ) ;
        $this->assertSame( 250     , $properties[1]->value ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnNullEmptyOrAssociativeInput(): void
    {
        $this->assertNull( hydrateAdditionalProperty() ) ;
        $this->assertNull( hydrateAdditionalProperty( [] ) ) ;
        $this->assertNull( hydrateAdditionalProperty( [ 'propertyID' => 'grain' ] ) ) ;
    }
}
