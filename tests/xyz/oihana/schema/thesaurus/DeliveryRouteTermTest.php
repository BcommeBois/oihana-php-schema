<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;
use org\schema\enumerations\DayOfWeek;
use org\schema\ParcelDelivery;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\DeliveryRouteTermTrait;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

class DeliveryRouteTermTest extends TestCase
{
    public function testDefaults(): void
    {
        $route = new DeliveryRouteTerm();

        $this->assertNull( $route->assignedPOS ?? null );
        $this->assertNull( $route->byDay       ?? null );
    }

    public function testIsThesaurusTerm(): void
    {
        $route = new DeliveryRouteTerm();

        $this->assertInstanceOf( ThesaurusTerm::class , $route );
        $this->assertInstanceOf( DefinedTerm::class   , $route );
    }

    public function testUsesDeliveryRouteTermTrait(): void
    {
        $this->assertContains( DeliveryRouteTermTrait::class , class_uses( DeliveryRouteTerm::class ) );
    }

    public function testContextInheritedFromThesaurusTerm(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , DeliveryRouteTerm::CONTEXT );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'assignedPOS' , DeliveryRouteTerm::ASSIGNED_POS );
        $this->assertSame( 'byDay'       , DeliveryRouteTerm::BY_DAY );
    }

    /**
     * The two keys reach the aggregator, and `assignedPOS` keeps the spelling the
     * library already uses for the point of sale a customer is attached to. A
     * divergent value would not be a style slip : both traits meet in
     * {@see Oihana}, where redeclaring one constant with two values is fatal at
     * class load — and nothing else in the suite would catch it.
     */
    public function testConstantsAreComposedIntoTheAggregator(): void
    {
        $this->assertSame( DeliveryRouteTerm::ASSIGNED_POS , Oihana::ASSIGNED_POS );
        $this->assertSame( DeliveryRouteTerm::BY_DAY       , Oihana::BY_DAY );
    }

    public function testConstructorCopiesScalarProperties(): void
    {
        $route = new DeliveryRouteTerm
        ([
            'id'                            => '01D' ,
            'name'                          => 'West coast, midweek' ,
            DeliveryRouteTerm::ASSIGNED_POS => '1' ,
            DeliveryRouteTerm::BY_DAY       => [ DayOfWeek::WEDNESDAY , DayOfWeek::FRIDAY ] ,
        ]);

        $this->assertSame( '01D'                 , $route->id );
        $this->assertSame( 'West coast, midweek' , $route->name );
        $this->assertSame( '1'                   , $route->assignedPOS );
        $this->assertSame( [ DayOfWeek::WEDNESDAY , DayOfWeek::FRIDAY ] , $route->byDay );
    }

    /**
     * An empty list is not the absence of an answer : a route defined but not yet
     * scheduled runs no day at all, which a consumer must be able to tell from a
     * route whose days were never read.
     */
    public function testEmptyByDayIsKeptAsAnEmptyList(): void
    {
        $route = new DeliveryRouteTerm([ DeliveryRouteTerm::BY_DAY => [] ]);

        $this->assertIsArray( $route->byDay );
        $this->assertCount( 0 , $route->byDay );
        $this->assertNotNull( $route->byDay );
    }

    /**
     * Through the reflection-based hydration path, `byDay` stays a plain array of
     * {@see DayOfWeek} values — no value-object wrapping, so a route hydrates
     * straight from a converted source row.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesByDayAsPlainArray(): void
    {
        $route = ( new Reflection() )->hydrate
        (
            [
                'name'                    => 'West coast, midweek' ,
                DeliveryRouteTerm::BY_DAY => [ DayOfWeek::WEDNESDAY , DayOfWeek::FRIDAY ] ,
            ],
            DeliveryRouteTerm::class
        );

        $this->assertIsArray( $route->byDay );
        $this->assertSame( [ DayOfWeek::WEDNESDAY , DayOfWeek::FRIDAY ] , $route->byDay );
    }

    /**
     * A bare warehouse reference is kept as read : nothing has been joined yet.
     *
     * @throws ReflectionException
     */
    public function testReflectionKeepsAssignedPosRawWhenScalar(): void
    {
        $route = ( new Reflection() )->hydrate
        (
            [ 'name' => 'West coast' , DeliveryRouteTerm::ASSIGNED_POS => '1' ] ,
            DeliveryRouteTerm::class
        );

        $this->assertSame( '1' , $route->assignedPOS );
    }

    /**
     * Once the reference data has been joined, the row resolves into a
     * {@see Warehouse} — the same shape
     * {@see \xyz\oihana\schema\organizations\Customer::$assignedPOS} follows.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesAssignedPosAsWarehouse(): void
    {
        $route = ( new Reflection() )->hydrate
        (
            [
                'name'                          => 'West coast' ,
                DeliveryRouteTerm::ASSIGNED_POS => [ 'id' => '1' , 'name' => 'Main warehouse' ] ,
            ],
            DeliveryRouteTerm::class
        );

        $this->assertInstanceOf( Warehouse::class , $route->assignedPOS );
        $this->assertSame( '1'              , $route->assignedPOS->id );
        $this->assertSame( 'Main warehouse' , $route->assignedPOS->name );
    }

    public function testAssignedPosAndByDayAssignment(): void
    {
        $route = new DeliveryRouteTerm();

        $route->assignedPOS = 400 ;
        $route->byDay       = [ DayOfWeek::MONDAY ] ;

        $this->assertSame( 400 , $route->assignedPOS );
        $this->assertSame( [ DayOfWeek::MONDAY ] , $route->byDay );
    }

    /**
     * A house route term is assignable to {@see ParcelDelivery::$hasDeliveryRoute} :
     * it reaches that union through {@see DefinedTerm}, so `org\schema` never has
     * to know about the thesaurus.
     */
    public function testIsAssignableToParcelDeliveryHasDeliveryRoute(): void
    {
        $route = new DeliveryRouteTerm();
        $route->name = 'West coast, midweek' ;

        $delivery = new ParcelDelivery();
        $delivery->hasDeliveryRoute = $route ;

        $this->assertSame( $route , $delivery->hasDeliveryRoute );
    }
}
