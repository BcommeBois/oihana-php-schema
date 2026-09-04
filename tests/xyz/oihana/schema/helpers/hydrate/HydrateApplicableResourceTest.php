<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ApplicableResource;
use xyz\oihana\schema\products\Product;

use function xyz\oihana\schema\helpers\hydrate\hydrateApplicableResource;

class HydrateApplicableResourceTest extends TestCase
{
    public function testHydratesAnArray(): void
    {
        $link = hydrateApplicableResource
        ([
            Oihana::ITEM               => [ Oihana::ID => 'srv-polish' ] ,
            Oihana::POSITION           => 1 ,
            Oihana::APPLIED_BY_DEFAULT => true ,
        ]);

        $this->assertInstanceOf( ApplicableResource::class , $link );
        $this->assertSame( 1 , $link->position );
        $this->assertTrue( $link->appliedByDefault );
    }

    /**
     * The point of the helper : the resource is typed down, not left as the
     * array it arrived as.
     */
    public function testTypesTheResourceItPointsAt(): void
    {
        $link = hydrateApplicableResource( [ Oihana::ITEM => [ Oihana::ID => 'srv-polish' ] ] ) ;

        $this->assertInstanceOf( Product::class , $link?->item );
        $this->assertSame( 'srv-polish' , $link->item->id );
    }

    /**
     * A resource already typed is left alone : re-wrapping it would rebuild
     * what a caller has, possibly, already enriched.
     */
    public function testLeavesAnAlreadyTypedResourceAlone(): void
    {
        $resource = new Product( [ Oihana::ID => 'srv-polish' , Oihana::NAME => 'Polishing' ] ) ;

        $link = hydrateApplicableResource( [ Oihana::ITEM => $resource ] ) ;

        $this->assertSame( $resource , $link?->item );
        $this->assertSame( 'Polishing' , $link->item->name );
    }

    public function testReturnsAnAlreadyTypedLinkAsItIs(): void
    {
        $link = new ApplicableResource( [ Oihana::POSITION => 3 ] ) ;

        $this->assertSame( $link , hydrateApplicableResource( $link ) );
    }

    public function testReturnsNullOnNothingToBuild(): void
    {
        $this->assertNull( hydrateApplicableResource( null ) );
        $this->assertNull( hydrateApplicableResource() );
        $this->assertNull( hydrateApplicableResource( 'srv-polish' ) );
        $this->assertNull( hydrateApplicableResource( 42 ) );
    }

    /**
     * A link with no resource at all is still a link — the source may name a
     * position and nothing else, and building nothing would lose that.
     */
    public function testHydratesALinkCarryingNoResource(): void
    {
        $link = hydrateApplicableResource( [ Oihana::POSITION => 2 ] ) ;

        $this->assertInstanceOf( ApplicableResource::class , $link );
        $this->assertNull( $link->item ?? null );
    }
}
