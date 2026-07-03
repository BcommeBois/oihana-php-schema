<?php

namespace tests\xyz\oihana\schema\places ;

use PHPUnit\Framework\TestCase;

use org\schema\GeoCoordinates;
use org\schema\PostalAddress;
use org\schema\PropertyValue;

use xyz\oihana\schema\constants\SiteAdditionalProperty;
use xyz\oihana\schema\places\Place;
use xyz\oihana\schema\places\Site;

class PlaceTest extends TestCase
{
    public function testIsSite(): void
    {
        $this->assertInstanceOf( Site::class , new Place() );
    }

    public function testMagicSetHandlesAdditionalProperties(): void
    {
        $place = new Place() ;

        $place->isBillingAddress = '1' ;

        $this->assertCount( 1 , $place->additionalProperty ) ;
        $this->assertInstanceOf( PropertyValue::class , $place->additionalProperty[0] ) ;
        $this->assertSame( SiteAdditionalProperty::IS_BILLING_ADDRESS , $place->additionalProperty[0]->propertyID ) ;
        $this->assertTrue( $place->additionalProperty[0]->value ) ;
    }

    public function testMagicSetHandlesGeoCoordinates(): void
    {
        $place = new Place() ;

        $place->geoLatitude = '43.4832' ;

        $this->assertInstanceOf( GeoCoordinates::class , $place->geo ) ;
        $this->assertSame( 43.4832 , $place->geo->latitude ) ;
    }

    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $place = new Place() ;

        $place->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $place->address ) ;
        $this->assertSame( '12 rue des Bois' , $place->address->streetAddress ) ;
    }

    public function testSetAdditionalPropertiesRejectsNonStringOrUnknownValues(): void
    {
        $place = new Place() ;

        $this->assertFalse( $place->setAdditionalProperties( SiteAdditionalProperty::IS_BILLING_ADDRESS , 1 ) ) ;
        $this->assertFalse( $place->setAdditionalProperties( 'unknownProperty' , '1' ) ) ;
        $this->assertNull( $place->additionalProperty ) ;
    }
}
