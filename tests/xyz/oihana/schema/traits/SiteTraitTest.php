<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;

use org\schema\GeoCoordinates;
use org\schema\PostalAddress;
use org\schema\PropertyValue;

use xyz\oihana\schema\constants\SiteAdditionalProperty;
use xyz\oihana\schema\traits\SiteTrait;

/**
 * Host exposing the {@see SiteTrait} with the properties expected by the
 * composed traits, mirroring how {@see \xyz\oihana\schema\places\CustomerSite} uses it.
 */
class SiteHost
{
    use SiteTrait ;

    public null|array|PropertyValue $additionalProperty = null ;

    public mixed $address = null ;

    public mixed $contactPoint = null ;

    public ?GeoCoordinates $geo = null ;
}

class SiteTraitTest extends TestCase
{
    public function testMagicSetHandlesAdditionalProperties(): void
    {
        $host = new SiteHost() ;

        $host->isDefaultAddress = '1' ;

        $this->assertCount( 1 , $host->additionalProperty ) ;
        $this->assertSame( SiteAdditionalProperty::IS_DEFAULT_ADDRESS , $host->additionalProperty[0]->propertyID ) ;
        $this->assertTrue( $host->additionalProperty[0]->value ) ;
    }

    public function testMagicSetHandlesGeoCoordinates(): void
    {
        $host = new SiteHost() ;

        $host->geoLatitude  = '43.4832' ;
        $host->geoLongitude = '-1.5586' ;

        $this->assertInstanceOf( GeoCoordinates::class , $host->geo ) ;
        $this->assertSame( 43.4832 , $host->geo->latitude  ) ;
        $this->assertSame( -1.5586 , $host->geo->longitude ) ;
    }

    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $host = new SiteHost() ;

        $host->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $host->address ) ;
        $this->assertSame( '12 rue des Bois' , $host->address->streetAddress ) ;
    }

    public function testMagicSetSilentlyIgnoresUnknownProperties(): void
    {
        $host = new SiteHost() ;

        $host->unknownProperty = 'value' ;

        $this->assertNull( $host->additionalProperty ) ;
        $this->assertNull( $host->address ) ;
        $this->assertNull( $host->geo ) ;
    }

    public function testSetAdditionalPropertiesRejectsNonStringOrUnknownValues(): void
    {
        $host = new SiteHost() ;

        $this->assertFalse( $host->setAdditionalProperties( SiteAdditionalProperty::IS_DEFAULT_ADDRESS , 1 ) ) ;
        $this->assertFalse( $host->setAdditionalProperties( 'unknownProperty' , '1' ) ) ;
        $this->assertNull( $host->additionalProperty ) ;
    }
}
