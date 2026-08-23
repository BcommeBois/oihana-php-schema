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

    /**
     * A lone instance is a legal shape of the `additionalProperty` property. The
     * signature used to be `?array`, so callers feeding it straight from that
     * property — as `hydrateCustomerSite()` does — raised a TypeError.
     *
     * @throws ReflectionException
     */
    public function testHandsBackAnythingThatIsNotAnArray(): void
    {
        $property = new PropertyValue( [ 'propertyID' => 'grain' ] ) ;

        $this->assertSame( $property , hydrateAdditionalProperty( $property ) ) ;
        $this->assertSame( 'ref-7'   , hydrateAdditionalProperty( 'ref-7' ) ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateAdditionalProperty( [ 'property-ref-42' , 'property-ref-42' ] ) ;

        $this->assertSame( [ 'property-ref-42' , 'property-ref-42' ] , $bare ) ;

        $mixed = hydrateAdditionalProperty( [ 'property-ref-42' , [ 'propertyID' => 'civility' , 'value' => 'Ms' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'property-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( PropertyValue::class , $mixed[ 1 ] ) ;

        // It used to be worse than a value lost : the constructor takes an array or an
        // object, never a string, so a handle in the list threw.
        $this->assertSame( [ 'a' , 'b' ] , hydrateAdditionalProperty( [ 'a' , 'b' ] ) ) ;
    }
}
