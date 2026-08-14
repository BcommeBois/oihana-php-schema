<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\AggregateOffer;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\enumerations\UnitOfSaleType;
use xyz\oihana\schema\products\PhysicalQuantity;

use function xyz\oihana\schema\helpers\hydrate\findPhysicalQuantityByType;
use function xyz\oihana\schema\helpers\hydrate\hydrateAggregateOffer;

final class FindPhysicalQuantityByTypeTest extends TestCase
{
    /**
     * The raw rows a base read leaves — the shape the walk meets most often,
     * since nothing types a chain before someone asks for it.
     *
     * @return array<string,mixed>
     */
    private function chain() :array
    {
        return
        [
            'additionalType' => UnitOfSaleType::UNIT ,
            'value'          => 1 ,
            'unitCode'       => 'MTK' ,
            'weight'         => 10.99 ,
            'volume'         => 0.0142 ,
            'valueReference' =>
            [
                'additionalType' => UnitOfSaleType::PACKAGE ,
                'value'          => 1.403 ,
                'unitCode'       => 'PK' ,
                'weight'         => 15.419 ,
                'volume'         => 0.0312 ,
                'valueReference' =>
                [
                    'additionalType' => UnitOfSaleType::PARCEL ,
                    'value'          => 84 ,
                    'unitCode'       => 'PF' ,
                    'weight'         => 1295.196 ,
                    'volume'         => 2.6208 ,
                ]
            ]
        ];
    }

    /**
     * A tree handed in as a parameter is walked, and the level found **keeps
     * its weight and its volume** — the one thing a walk written by hand loses,
     * since a plain QuantitativeValue declares neither.
     *
     * @throws ReflectionException
     */
    public function testWalksARawTreeAndKeepsTheMeasuresOfTheLevelFound(): void
    {
        $parcel = findPhysicalQuantityByType( UnitOfSaleType::PARCEL , $this->chain() ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $parcel ) ;
        $this->assertSame( 84         , $parcel->value    ) ;
        $this->assertSame( 'PF'       , $parcel->unitCode ) ;
        $this->assertSame( 1295.196   , $parcel->weight   ) ;
        $this->assertSame( 2.6208     , $parcel->volume   ) ;
    }

    /**
     * The level found is typed, and so is everything below it : the walk hands
     * back the shape `Reflection::hydrate()` builds, not a typed head sitting
     * over raw rows. Read one way at the top and another one step down, a chain
     * whose whole point is the ratio between two levels answers `null` on an
     * ordinary `->weight`, without an error.
     *
     * @throws ReflectionException
     */
    public function testTheLevelFoundTypesTheChainBelowIt(): void
    {
        $unit = findPhysicalQuantityByType( UnitOfSaleType::UNIT , $this->chain() ) ;

        $package = $unit->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 0.0312 , $package->volume ) ;

        $parcel = $package->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $parcel ) ;
        $this->assertSame( 1295.196 , $parcel->weight ) ;
        $this->assertSame( 2.6208   , $parcel->volume ) ;
    }

    /**
     * The same tree, typed : a chain already hydrated is walked exactly the
     * same way, and gives back the same level.
     *
     * @throws ReflectionException
     */
    public function testWalksATypedTreeAndKeepsTheMeasuresOfTheLevelFound(): void
    {
        $tree = new PhysicalQuantity( $this->chain() ) ;

        $package = findPhysicalQuantityByType( UnitOfSaleType::PACKAGE , $tree ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 1.403  , $package->value  ) ;
        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 0.0312 , $package->volume ) ;
    }

    /**
     * The first level is a level like any other, and it is found without the
     * walk going anywhere.
     *
     * @throws ReflectionException
     */
    public function testFindsTheHeadOfTheChain(): void
    {
        $unit = findPhysicalQuantityByType( UnitOfSaleType::UNIT , $this->chain() ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $unit ) ;
        $this->assertSame( 10.99  , $unit->weight ) ;
        $this->assertSame( 0.0142 , $unit->volume ) ;
    }

    /**
     * 🔑 The reason the walk is exposed at all : the tree is `null` on a product
     * read back from a base, and lives on the offer beside it. The consumer
     * holding that copy walks it without owning a product — and without
     * rebuilding the levels as plain quantities, which would drop both measures
     * without an error and without a trace.
     *
     * @throws ReflectionException
     */
    public function testWalksTheTreeCarriedByAnOffer(): void
    {
        $offer = hydrateAggregateOffer([ 'eligibleQuantity' => $this->chain() ]) ;

        $this->assertInstanceOf( AggregateOffer::class , $offer ) ;

        $parcel = findPhysicalQuantityByType( UnitOfSaleType::PARCEL , $offer->eligibleQuantity ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $parcel ) ;
        $this->assertSame( 1295.196 , $parcel->weight ) ;
        $this->assertSame( 2.6208   , $parcel->volume ) ;
    }

    /**
     * A type no level carries ends the walk on null, rather than on the last
     * level met.
     *
     * @throws ReflectionException
     */
    public function testAChainThatHoldsTheTypeNowhereAnswersNull(): void
    {
        $this->assertNull( findPhysicalQuantityByType( 'pallet' , $this->chain() ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testAnAbsentChainAnswersNull(): void
    {
        $this->assertNull( findPhysicalQuantityByType( UnitOfSaleType::UNIT ) ) ;
        $this->assertNull( findPhysicalQuantityByType( UnitOfSaleType::UNIT , null ) ) ;
        $this->assertNull( findPhysicalQuantityByType( UnitOfSaleType::UNIT , [] ) ) ;
    }

    /**
     * ⚠️ Schema.org lets `valueReference` hold things that are not quantities at
     * all — a bare code, an enumeration. Such a value ends the walk instead of
     * being read as a level.
     *
     * @throws ReflectionException
     */
    public function testAReferenceThatIsNotALevelEndsTheWalk(): void
    {
        $tree =
        [
            'additionalType' => UnitOfSaleType::UNIT ,
            'valueReference' => 'PK' ,
        ];

        $this->assertNull( findPhysicalQuantityByType( UnitOfSaleType::PACKAGE , $tree ) ) ;
    }

    /**
     * A level that is a plain mirror quantity is walked too : the guard is on
     * the shape of the chain, not on the class that types it.
     *
     * @throws ReflectionException
     */
    public function testWalksThroughAMirrorTypedLevel(): void
    {
        $tree = new QuantitativeValue
        ([
            'additionalType' => UnitOfSaleType::UNIT ,
            'valueReference' => new QuantitativeValue
            ([
                'additionalType' => UnitOfSaleType::PACKAGE ,
                'value'          => 12 ,
            ]) ,
        ]);

        $package = findPhysicalQuantityByType( UnitOfSaleType::PACKAGE , $tree ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 12 , $package->value ) ;
    }
}
