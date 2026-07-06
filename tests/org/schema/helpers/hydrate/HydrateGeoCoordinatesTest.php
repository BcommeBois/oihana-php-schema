<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\GeoCoordinates;

use function org\schema\helpers\hydrate\hydrateGeoCoordinates;

final class HydrateGeoCoordinatesTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleDefinition(): void
    {
        $geo = hydrateGeoCoordinates( [ 'latitude' => 43.4696 , 'longitude' => -1.5531 ] ) ;

        $this->assertInstanceOf( GeoCoordinates::class , $geo ) ;
        $this->assertSame( 43.4696 , $geo->latitude ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayOfDefinitions(): void
    {
        $coordinates = hydrateGeoCoordinates(
        [
            [ 'latitude' => 43.4696 , 'longitude' => -1.5531 ] ,
            [ 'latitude' => 44.8378 , 'longitude' => -0.5792 ] ,
        ]) ;

        $this->assertIsArray( $coordinates ) ;
        $this->assertCount( 2 , $coordinates ) ;
        $this->assertContainsOnlyInstancesOf( GeoCoordinates::class , $coordinates ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFiltersNonCoordinateEntriesAndNullifiesAnEmptyResult(): void
    {
        $this->assertNull( hydrateGeoCoordinates( [ 'foo' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateGeoCoordinates() ) ;
        $this->assertSame( 'raw' , hydrateGeoCoordinates( 'raw' ) ) ;
    }
}
