<?php

namespace tests\xyz\oihana\schema\places ;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use org\schema\ContactPoint;

use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\places\JobSite;
use xyz\oihana\schema\places\Office;
use xyz\oihana\schema\places\Place;
use xyz\oihana\schema\places\ProviderSite;
use xyz\oihana\schema\places\Site;
use xyz\oihana\schema\places\Warehouse;

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
}
