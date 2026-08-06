<?php

namespace tests\xyz\oihana\schema\places ;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

use oihana\reflect\Reflection;

use org\schema\ContactPoint;

use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\places\JobSite;
use xyz\oihana\schema\places\Office;
use xyz\oihana\schema\places\Place;
use xyz\oihana\schema\places\ProviderSite;
use xyz\oihana\schema\places\Site;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;

class SiteTest extends TestCase
{
    /**
     * Every site flavour composes SetContactPointTrait, which reads and writes
     * `contactPoint` — so the property has to exist on all of them.
     *
     * @return string[]
     */
    public static function siteFlavours(): array
    {
        return
        [
            [ Site::class         ] ,
            [ Place::class        ] ,
            [ CustomerSite::class ] ,
            [ JobSite::class      ] ,
            [ Office::class       ] ,
            [ ProviderSite::class ] ,
            [ Warehouse::class    ] ,
        ];
    }

    #[DataProvider( 'siteFlavours' )]
    public function testDeclaresContactPoint( string $class ): void
    {
        $this->assertTrue
        (
            property_exists( $class , 'contactPoint' ) ,
            sprintf( '%s has no contactPoint property.' , $class )
        );
    }

    /**
     * It is declared once, on `Site`, and inherited from there — no flavour
     * shadows it with its own copy.
     */
    #[DataProvider( 'siteFlavours' )]
    public function testContactPointIsDeclaredOnceOnSite( string $class ): void
    {
        $property = new ReflectionProperty( $class , 'contactPoint' );

        $this->assertSame( Site::class , $property->getDeclaringClass()->getName() );
    }

    /**
     * The real symptom of the missing declaration : these classes define `__set`,
     * so writing an undeclared property went to that hook instead of raising a
     * dynamic-property deprecation — and `__set` routes additional properties,
     * geo coordinates and postal addresses only. The contact was silently dropped.
     */
    public function testStoresAContactPointInsteadOfDroppingItThroughMagicSet(): void
    {
        $office = new Office();

        $stored = Closure::bind
        (
            fn( string $name , mixed $value ): bool => $this->setContactPointProperty( $name , $value ) ,
            $office ,
            Office::class
        )( 'mobile' , '0612345678' );

        $this->assertTrue( $stored );

        $this->assertIsArray( $office->contactPoint );
        $this->assertCount( 1 , $office->contactPoint );

        $this->assertInstanceOf( ContactPoint::class , $office->contactPoint[ 0 ] );
        $this->assertSame( '0612345678' , $office->contactPoint[ 0 ]->telephone );
    }

    public function testContactPointDefaultsToNull(): void
    {
        $this->assertNull( ( new Site() )->contactPoint ?? null );
    }

    public function testDeliveryRouteDefaultsToNull(): void
    {
        $this->assertNull( ( new Site() )->deliveryRoute ?? null );
    }

    /**
     * Declared on `Site` like the other shared members, so every flavour of site
     * can be served by a route — a customer address, a construction site, a
     * warehouse taking an internal transfer.
     */
    #[DataProvider( 'siteFlavours' )]
    public function testDeliveryRouteIsDeclaredOnceOnSite( string $class ): void
    {
        $property = new ReflectionProperty( $class , 'deliveryRoute' );

        $this->assertSame( Site::class , $property->getDeclaringClass()->getName() );
    }

    /**
     * `#[HydrateWith]` turns the stored rows into assignments : the property is a
     * list because a same address is commonly served by more than one route.
     *
     * @throws ReflectionException
     */
    public function testDeliveryRouteHydratesIntoAssignments(): void
    {
        $site = ( new Reflection() )->hydrate
        (
            [
                'deliveryRoute' =>
                [
                    [ DeliveryRouteAssignment::ROUTE => '01D' , DeliveryRouteAssignment::POSITION => 12 ] ,
                    [ DeliveryRouteAssignment::ROUTE => '03'  ] ,
                ],
            ],
            CustomerSite::class
        );

        $this->assertIsArray( $site->deliveryRoute );
        $this->assertCount( 2 , $site->deliveryRoute );
        $this->assertContainsOnlyInstancesOf( DeliveryRouteAssignment::class , $site->deliveryRoute );

        $this->assertSame( '01D' , $site->deliveryRoute[ 0 ]->route );
        $this->assertSame( 12    , $site->deliveryRoute[ 0 ]->position );
        $this->assertSame( '03'  , $site->deliveryRoute[ 1 ]->route );
    }

    /**
     * The route a site is served by and the method an order travels under are two
     * different answers : setting one leaves the other alone.
     */
    public function testDeliveryRouteAndDeliveryMethodAreIndependent(): void
    {
        $site = new Site();

        $site->deliveryMethod = 'OnSitePickup' ;
        $site->deliveryRoute  = [ new DeliveryRouteAssignment([ DeliveryRouteAssignment::ROUTE => '01D' ]) ] ;

        $this->assertSame( 'OnSitePickup' , $site->deliveryMethod );
        $this->assertCount( 1 , $site->deliveryRoute );
        $this->assertSame( '01D' , $site->deliveryRoute[ 0 ]->route );
    }
}
