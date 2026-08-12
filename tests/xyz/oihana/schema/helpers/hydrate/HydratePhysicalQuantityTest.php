<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\products\PhysicalQuantity;

use function xyz\oihana\schema\helpers\hydrate\hydratePhysicalQuantity;

final class HydratePhysicalQuantityTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testTypesEveryLevelOfTheChain(): void
    {
        $quantity = hydratePhysicalQuantity(
        [
            'value'    => 1 ,
            'unitCode' => 'MTK' ,
            'weight'   => 10.99 ,
            'valueReference' =>
            [
                'value'    => 1.403 ,
                'unitCode' => 'PK' ,
                'weight'   => 15.419 ,
                'volume'   => 0.0312 ,
                'valueReference' =>
                [
                    'value'    => 84 ,
                    'unitCode' => 'PF' ,
                    'weight'   => 1295.2 ,
                ]
            ]
        ]) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $quantity ) ;
        $this->assertSame( 10.99 , $quantity->weight ) ;

        $package = $quantity->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 0.0312 , $package->volume ) ;

        $pallet = $package->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $pallet ) ;
        $this->assertSame( 1295.2 , $pallet->weight ) ;

        // the ratio between two levels restates the packaging chain
        $this->assertEqualsWithDelta( 84 , $pallet->weight / $package->weight , 0.01 ) ;
    }

    /**
     * A chain that stops types what it has and says nothing more.
     * @throws ReflectionException
     */
    public function testASingleLevelHasNoReferenceToType(): void
    {
        $quantity = hydratePhysicalQuantity( [ 'value' => 1 , 'unitCode' => 'C62' ] ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $quantity ) ;
        $this->assertSame( 'C62' , $quantity->unitCode ) ;
        $this->assertNull( $quantity->valueReference ?? null ) ;
    }

    /**
     * A level already typed is handed back as it stands : re-wrapping it would
     * rebuild what a caller may already have enriched.
     * @throws ReflectionException
     */
    public function testAnAlreadyTypedLevelIsLeftAlone(): void
    {
        $typed = new PhysicalQuantity([ 'value' => 1 , 'weight' => 10.99 ]) ;

        $this->assertSame( $typed , hydratePhysicalQuantity( $typed ) ) ;

        $chain = hydratePhysicalQuantity( [ 'value' => 1 , 'valueReference' => $typed ] ) ;

        $this->assertSame( $typed , $chain->valueReference ) ;
    }

    /**
     * A `QuantitativeValue` sitting on the chain is not rebuilt either — the
     * guard is on the mirror type, so nothing a consumer assigned is replaced.
     * @throws ReflectionException
     */
    public function testAMirrorTypedLevelIsNotRebuilt(): void
    {
        $mirror = new QuantitativeValue([ 'value' => 12 ]) ;

        $chain = hydratePhysicalQuantity( [ 'value' => 1 , 'valueReference' => $mirror ] ) ;

        $this->assertSame( $mirror , $chain->valueReference ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnAnythingThatIsNotAChain(): void
    {
        $this->assertNull( hydratePhysicalQuantity() ) ;
        $this->assertNull( hydratePhysicalQuantity( null ) ) ;
        $this->assertNull( hydratePhysicalQuantity( 'PK' ) ) ;
    }
}
