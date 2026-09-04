<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;

use org\schema\Intangible;
use org\schema\ListItem;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ApplicableResource;
use xyz\oihana\schema\products\Product;

class ApplicableResourceTest extends TestCase
{
    public function testIsAnIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new ApplicableResource() );
    }

    /**
     * 🚨 It carries the shape of a {@see ListItem} without extending it : that
     * class types `item` as `?Thing`, and PHP forbids widening an inherited
     * property to `null|array|Thing` — which every constructor of this library
     * needs, since they all build from arrays.
     */
    public function testIsNotAListItem(): void
    {
        $this->assertNotInstanceOf( ListItem::class , new ApplicableResource() );
    }

    /**
     * The constructor path is the one the inheritance would have broken.
     */
    public function testTheConstructorAcceptsAResourceAsAnArray(): void
    {
        $link = new ApplicableResource( [ Oihana::ITEM => [ Oihana::ID => 'srv-polish' ] ] ) ;

        $this->assertIsArray( $link->item );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ApplicableResource::CONTEXT );
    }

    public function testTraitConstant(): void
    {
        $this->assertSame( 'appliedByDefault' , Oihana::APPLIED_BY_DEFAULT );
    }

    /**
     * `item` and `position` come from the mirror and are not redeclared, so the
     * inherited names have to keep answering.
     */
    public function testInheritedConstants(): void
    {
        $this->assertSame( 'item'     , Oihana::ITEM     );
        $this->assertSame( 'position' , Oihana::POSITION );
    }

    public function testDefaults(): void
    {
        $link = new ApplicableResource() ;

        $this->assertNull( $link->appliedByDefault ?? null );
        $this->assertNull( $link->item             ?? null );
        $this->assertNull( $link->position         ?? null );
    }

    public function testCarriesTheResourceItsRankAndItsFlag(): void
    {
        $link = new ApplicableResource
        ([
            Oihana::ITEM               => new Product( [ Oihana::ID => 'srv-polish' ] ) ,
            Oihana::POSITION           => 1 ,
            Oihana::APPLIED_BY_DEFAULT => true ,
        ]);

        $this->assertInstanceOf( Product::class , $link->item );
        $this->assertSame( 'srv-polish' , $link->item->id );
        $this->assertSame( 1            , $link->position );
        $this->assertTrue( $link->appliedByDefault );
    }

    /**
     * 🚨 An absent flag is not a `false`. Absent says « the source does not
     * tell », `false` says « the source says no » — a consumer that needs to
     * tell one from the other must be able to.
     */
    public function testAnAbsentFlagIsNotAFalseOne(): void
    {
        $silent = new ApplicableResource( [ Oihana::POSITION => 2 ] ) ;
        $denied = new ApplicableResource( [ Oihana::POSITION => 2 , Oihana::APPLIED_BY_DEFAULT => false ] ) ;

        $this->assertNull ( $silent->appliedByDefault ?? null );
        $this->assertFalse( $denied->appliedByDefault );

        $this->assertArrayNotHasKey( Oihana::APPLIED_BY_DEFAULT , $silent->jsonSerialize() );
        $this->assertArrayHasKey   ( Oihana::APPLIED_BY_DEFAULT , $denied->jsonSerialize() );
    }

    /**
     * The flag is the whole reason this class exists : the same resource is
     * applied by default on one host and merely offered on the next, so two
     * links pointing at the same resource must be able to disagree.
     */
    public function testTwoLinksOnTheSameResourceMayDisagree(): void
    {
        $resource = new Product( [ Oihana::ID => 'srv-treatment' ] ) ;

        $included = new ApplicableResource( [ Oihana::ITEM => $resource , Oihana::APPLIED_BY_DEFAULT => true  ] ) ;
        $offered  = new ApplicableResource( [ Oihana::ITEM => $resource , Oihana::APPLIED_BY_DEFAULT => false ] ) ;

        $this->assertSame( $included->item , $offered->item );
        $this->assertNotSame( $included->appliedByDefault , $offered->appliedByDefault );
    }
}
