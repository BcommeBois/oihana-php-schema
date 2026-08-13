<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\Organization;
use org\schema\Person;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\FeeUnresolvedReason;
use xyz\oihana\schema\products\FeeSpecification;

use function xyz\oihana\schema\helpers\hydrate\hydrateFeeSpecification;

final class HydrateFeeSpecificationTest extends TestCase
{
    /**
     * The pair the class exists for : an amount charged by the piece, and the
     * rate it derives from, published by the tonne. Both come out typed, so a
     * consumer reads `->price` on either side.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTypesTheRateBesideThePrice(): void
    {
        $fee = hydrateFeeSpecification(
        [
            'price'     => 0.0157 ,
            'unitCode'  => 'C62' ,
            'rate'      => [ 'price' => 215 , 'priceCurrency' => 'EUR' , 'unitCode' => 'TNE' ] ,
        ]) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertSame( 0.0157 , $fee->price    ) ;
        $this->assertSame( 'C62'  , $fee->unitCode ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;
        $this->assertSame( 215   , $fee->rate->price    ) ;
        $this->assertSame( 'EUR' , $fee->rate->priceCurrency ) ;
        $this->assertSame( 'TNE' , $fee->rate->unitCode ) ;
    }

    /**
     * A rate handed over already typed is the caller's object, possibly
     * enriched : re-wrapping it would rebuild it from scratch.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testLeavesAnAlreadyTypedRateAlone(): void
    {
        $rate = new UnitPriceSpecification([ 'price' => 215 , 'unitCode' => 'TNE' ]) ;

        $fee = hydrateFeeSpecification( [ 'price' => 0.0157 , 'rate' => $rate ] ) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertSame( $rate , $fee->rate ) ;
    }

    /**
     * The issuing body is read from the payload's `@type`, not from the
     * declared union — which would always answer Organization.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTypesThePublisherFromItsType(): void
    {
        $fee = hydrateFeeSpecification(
        [
            'price'     => 0.0157 ,
            'publisher' => [ '@type' => 'Organization' , 'name' => 'Recycling Body' ] ,
        ]) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertInstanceOf( Organization::class , $fee->publisher ) ;
        $this->assertSame( 'Recycling Body' , $fee->publisher->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTypesAPersonPublisher(): void
    {
        $fee = hydrateFeeSpecification(
        [
            'price'     => 12 ,
            'publisher' => [ '@type' => 'Person' , 'name' => 'Jean Dupont' ] ,
        ]) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertInstanceOf( Person::class , $fee->publisher ) ;
        $this->assertSame( 'Jean Dupont' , $fee->publisher->name ) ;
    }

    /**
     * A publisher given as a plain reference — a key, a URL — is not a payload
     * to hydrate : it is handed back as it stands.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAStringPublisherAsItStands(): void
    {
        $fee = hydrateFeeSpecification( [ 'price' => 12 , 'publisher' => 'organizations/1234' ] ) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertSame( 'organizations/1234' , $fee->publisher ) ;
    }

    /**
     * A fee that is owed but cannot be quantified : no `price`, the published
     * rate typed all the same, and the reason kept.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTypesTheRateOfAnUnresolvedFee(): void
    {
        $fee = hydrateFeeSpecification(
        [
            'unitCode'         => 'C62' ,
            'rate'             => [ 'price' => 88 , 'unitCode' => 'TNE' ] ,
            'unresolvedReason' => FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT ,
        ]) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertNull( $fee->price ?? null ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;
        $this->assertSame( 88 , $fee->rate->price ) ;

        $this->assertSame( FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT , $fee->unresolvedReason ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAMinimalFeeUntouched(): void
    {
        $fee = hydrateFeeSpecification( [ Oihana::PRICE => 0.35 , Oihana::UNIT_CODE => 'MTK' ] ) ;

        $this->assertInstanceOf( FeeSpecification::class , $fee ) ;
        $this->assertSame( 0.35  , $fee->price    ) ;
        $this->assertSame( 'MTK' , $fee->unitCode ) ;

        $this->assertNull( $fee->rate      ?? null ) ;
        $this->assertNull( $fee->publisher ?? null ) ;
    }

    /**
     * An instance already built is completed, not rebuilt : whoever holds it
     * keeps holding it, with its nested references typed.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testCompletesAnInstanceInPlace(): void
    {
        $init = new FeeSpecification
        ([
            'price'     => 0.0157 ,
            'rate'      => [ 'price' => 215 , 'unitCode' => 'TNE' ] ,
            'publisher' => [ '@type' => 'Organization' , 'name' => 'Recycling Body' ] ,
        ]) ;

        $fee = hydrateFeeSpecification( $init ) ;

        $this->assertSame( $init , $fee ) ;
        $this->assertInstanceOf( UnitPriceSpecification::class , $fee->rate ) ;
        $this->assertInstanceOf( Organization::class , $fee->publisher ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsNullOnAnUnsupportedInput(): void
    {
        $this->assertNull( hydrateFeeSpecification() ) ;
        $this->assertNull( hydrateFeeSpecification( 'raw' ) ) ;
        $this->assertNull( hydrateFeeSpecification( 42 ) ) ;
    }
}
