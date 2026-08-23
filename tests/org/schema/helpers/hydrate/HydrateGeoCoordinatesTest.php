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
    public function testKeepsBareReferencesAndNullifiesAnEmptyResult(): void
    {
        $this->assertSame( [ 'geo-ref-42' ] , hydrateGeoCoordinates( [ 'geo-ref-42' ] ) ) ;
        $this->assertNull( hydrateGeoCoordinates( [ null ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateGeoCoordinates() ) ;
        $this->assertSame( 'raw' , hydrateGeoCoordinates( 'raw' ) ) ;
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
        $bare = hydrateGeoCoordinates( [ 'geo-ref-42' , 'geo-ref-42' ] ) ;

        $this->assertSame( [ 'geo-ref-42' , 'geo-ref-42' ] , $bare ) ;

        $mixed = hydrateGeoCoordinates( [ 'geo-ref-42' , [ 'latitude' => 43.4696 , 'longitude' => -1.5531 ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'geo-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( GeoCoordinates::class , $mixed[ 1 ] ) ;
    }
}
