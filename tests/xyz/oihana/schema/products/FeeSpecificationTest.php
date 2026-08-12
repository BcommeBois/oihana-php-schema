<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\FeeUnresolvedReason;
use xyz\oihana\schema\products\ExtraPriceSpecification;
use xyz\oihana\schema\products\FeeSpecification;

class FeeSpecificationTest extends TestCase
{
    public function testIsUnitPriceSpecification(): void
    {
        $this->assertInstanceOf( UnitPriceSpecification::class , new FeeSpecification() );
    }

    /**
     * Both extend `UnitPriceSpecification` and they have nothing to do with
     * each other : one serves price segmentation, the other fees.
     */
    public function testIsNotAnExtraPriceSpecification(): void
    {
        $this->assertNotInstanceOf( ExtraPriceSpecification::class , new FeeSpecification() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , FeeSpecification::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'rate'             , Oihana::RATE              );
        $this->assertSame( 'unresolvedReason' , Oihana::UNRESOLVED_REASON );
    }

    public function testDefaults(): void
    {
        $fee = new FeeSpecification() ;

        $this->assertNull( $fee->rate             ?? null );
        $this->assertNull( $fee->unresolvedReason ?? null );
    }

    /**
     * The common case : the rate is published in the very unit the item is
     * billed in, so `price` and `rate` carry the same figure and applying the
     * fee is `quantity × price`.
     */
    public function testAPricedFeeCarriesTheRateItDerivesFrom(): void
    {
        $fee = new FeeSpecification
        ([
            Oihana::PRICE          => 0.35 ,
            Oihana::PRICE_CURRENCY => 'EUR' ,
            Oihana::UNIT_CODE      => 'MTK' ,
            Oihana::RATE           => new UnitPriceSpecification
            ([
                Oihana::PRICE          => 0.35 ,
                Oihana::PRICE_CURRENCY => 'EUR' ,
                Oihana::UNIT_CODE      => 'MTK' ,
            ]) ,
        ]);

        $this->assertSame( 0.35  , $fee->price    ) ;
        $this->assertSame( 'MTK' , $fee->unitCode ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;
        $this->assertSame( 0.35 , $fee->rate->price ) ;
    }

    /**
     * The rate keeps its own unit even when the item is billed in another :
     * 215 EUR the tonne, charged 0.0157 EUR the piece. Storing only the second
     * would leave a charged amount with nothing to explain it.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesARateStatedInItsOwnUnit(): void
    {
        $fee = new Reflection()->hydrate
        (
            [
                Oihana::PRICE     => 0.0157 ,
                Oihana::UNIT_CODE => 'C62' ,
                Oihana::RATE      => [ Oihana::PRICE => 215 , Oihana::UNIT_CODE => 'TNE' ] ,
            ],
            FeeSpecification::class
        );

        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;

        $this->assertSame( 'C62' , $fee->unitCode       ) ;
        $this->assertSame( 'TNE' , $fee->rate->unitCode ) ;
        $this->assertSame( 215   , $fee->rate->price    ) ;
    }

    /**
     * A fee that is due but cannot be quantified : no `price`, the published
     * rate, and the reason. A consumer multiplying a quantity by an absent
     * price gets zero or an error — never a wrong amount.
     */
    public function testAnUnresolvedFeeHasNoPriceButSaysWhy(): void
    {
        $fee = new FeeSpecification
        ([
            Oihana::UNIT_CODE         => 'C62' ,
            Oihana::RATE              => new UnitPriceSpecification([ Oihana::PRICE => 88 , Oihana::UNIT_CODE => 'TNE' ]) ,
            Oihana::UNRESOLVED_REASON => FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT ,
        ]);

        $this->assertNull( $fee->price ?? null ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;
        $this->assertSame( 88 , $fee->rate->price ) ;

        $this->assertSame( FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT , $fee->unresolvedReason ) ;
    }
}
