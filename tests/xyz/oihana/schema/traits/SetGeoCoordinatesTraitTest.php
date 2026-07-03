<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;

use org\schema\GeoCoordinates;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\traits\SetGeoCoordinatesTrait;

/**
 * Host exposing the {@see SetGeoCoordinatesTrait} with the `geo` property
 * expected by the trait.
 */
class SetGeoCoordinatesHost
{
    use SetGeoCoordinatesTrait ;

    public ?GeoCoordinates $geo = null ;
}

class SetGeoCoordinatesTraitTest extends TestCase
{
    public function testSetGeoCoordinatesPropertyCreatesTheGeoCoordinates(): void
    {
        $host = new SetGeoCoordinatesHost() ;

        $this->assertTrue( $host->setGeoCoordinatesProperty( 'latitude' , '43.4832' ) ) ;

        $this->assertInstanceOf( GeoCoordinates::class , $host->geo ) ;
        $this->assertSame( 43.4832 , $host->geo->latitude ) ;
    }

    public function testSetGeoCoordinatesPropertyClearsANonNumericValue(): void
    {
        $host = new SetGeoCoordinatesHost() ;
        $host->setGeoCoordinatesProperty( 'latitude' , '43.4832' ) ;

        $this->assertTrue( $host->setGeoCoordinatesProperty( 'latitude' , null ) ) ;
        $this->assertNull( $host->geo->latitude ) ;
    }

    public function testSetGeoCoordinatesPropertyIgnoresANonNumericValueWithoutGeo(): void
    {
        $host = new SetGeoCoordinatesHost() ;

        $this->assertFalse( $host->setGeoCoordinatesProperty( 'latitude' , 'not-a-number' ) ) ;
        $this->assertNull( $host->geo ) ;
    }

    public function testSetGeoCoordinatesPropertiesMapsTheCustomProperties(): void
    {
        $host = new SetGeoCoordinatesHost() ;

        $this->assertTrue( $host->setGeoCoordinatesProperties( Oihana::GEO_LATITUDE  , '43.4832' ) ) ;
        $this->assertTrue( $host->setGeoCoordinatesProperties( Oihana::GEO_LONGITUDE , '-1.5586' ) ) ;
        $this->assertTrue( $host->setGeoCoordinatesProperties( Oihana::GEO_ELEVATION , '12'      ) ) ;
        $this->assertTrue( $host->setGeoCoordinatesProperties( Oihana::GEO_DISTANCE  , '2.4'     ) ) ;

        $this->assertSame( 43.4832 , $host->geo->latitude  ) ;
        $this->assertSame( -1.5586 , $host->geo->longitude ) ;
        $this->assertSame( 12.0    , $host->geo->elevation ) ;
    }

    public function testSetGeoCoordinatesPropertiesRejectsUnknownProperty(): void
    {
        $host = new SetGeoCoordinatesHost() ;

        $this->assertFalse( $host->setGeoCoordinatesProperties( 'unknownProperty' , '43.4832' ) ) ;
        $this->assertNull( $host->geo ) ;
    }
}
