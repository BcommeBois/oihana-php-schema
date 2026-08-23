<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\business\documents\DocumentTotals;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentTotals;

final class HydrateDocumentTotalsTest extends TestCase
{
    /**
     * Every amount the class declares is typed by the single call — the whole point
     * of going through reflection rather than the constructor.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesEveryAmountOfTheTotals(): void
    {
        $totals = hydrateDocumentTotals(
        [
            'allowanceTotal' => [ 'value' => 5.0   , 'currency' => 'EUR' ] ,
            'balanceDue'     => [ 'value' => 62.4  , 'currency' => 'EUR' ] ,
            'chargeTotal'    => [ 'value' => 52.0  , 'currency' => 'EUR' ] ,
            'prepaidAmount'  => [ 'value' => 10.0  , 'currency' => 'EUR' ] ,
            'subtotal'       => [ 'value' => 100.0 , 'currency' => 'EUR' ] ,
            'total'          => [ 'value' => 182.4 , 'currency' => 'EUR' ] ,
            'totalTax'       => [ 'value' => 30.4  , 'currency' => 'EUR' ] ,
        ]) ;

        $this->assertInstanceOf( DocumentTotals::class , $totals ) ;

        foreach ( [ 'allowanceTotal' , 'balanceDue' , 'chargeTotal' , 'prepaidAmount' , 'subtotal' , 'total' , 'totalTax' ] as $property )
        {
            $this->assertInstanceOf( MonetaryAmount::class , $totals->{ $property } , $property ) ;
        }

        $this->assertSame( 100.0 , $totals->subtotal->value    ) ;
        $this->assertSame( 'EUR' , $totals->subtotal->currency ) ;
        $this->assertSame( 182.4 , $totals->total->value       ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsMinimalTotalsUntouched(): void
    {
        $totals = hydrateDocumentTotals( [ 'subtotal' => [ 'value' => 0 , 'currency' => 'EUR' ] ] ) ;

        $this->assertInstanceOf( DocumentTotals::class , $totals ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->subtotal ) ;
        $this->assertNull( $totals->total ?? null ) ;
    }

    /**
     * The list shape has no consumer today, and answers all the same rather than
     * handing a list back to the caller untouched.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $totals = hydrateDocumentTotals(
        [
            [ 'subtotal' => [ 'value' => 10.0 , 'currency' => 'EUR' ] ] ,
            [ 'total'    => [ 'value' => 12.0 , 'currency' => 'EUR' ] ] ,
        ]) ;

        $this->assertIsArray( $totals ) ;
        $this->assertCount( 2 , $totals ) ;
        $this->assertContainsOnlyInstancesOf( DocumentTotals::class , $totals ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $totals[ 0 ]->subtotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals[ 1 ]->total    ) ;

        // A bare reference is kept — in a list as much as on its own. Only an entry that
        // resolved to nothing is dropped.
        $this->assertSame( [ 'totals-ref-42' ] , hydrateDocumentTotals( [ 'totals-ref-42' ] ) ) ;
        $this->assertNull( hydrateDocumentTotals( [ null ] ) ) ;
    }

    /**
     * A document that carries no totals says so with an absent value, not with an
     * object of empty amounts — unlike the lines and the adjustments, whose empty
     * list is an answer of its own.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAnEmptyArrayYieldsNull(): void
    {
        $this->assertNull( hydrateDocumentTotals( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAlreadyHydratedTotals(): void
    {
        $totals = new DocumentTotals( [ 'subtotal' => [ 'value' => 1 , 'currency' => 'EUR' ] ] ) ;

        $this->assertSame( $totals , hydrateDocumentTotals( $totals ) ) ;
        $this->assertSame( [ $totals ] , hydrateDocumentTotals( [ $totals ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateDocumentTotals() ) ;
        $this->assertSame( 'raw' , hydrateDocumentTotals( 'raw' ) ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateDocumentTotals( [ 'totals-ref-42' , 'totals-ref-42' ] ) ;

        $this->assertSame( [ 'totals-ref-42' , 'totals-ref-42' ] , $bare ) ;

        $mixed = hydrateDocumentTotals( [ 'totals-ref-42' , [ 'subtotal' => [ 'value' => 10.0 , 'currency' => 'EUR' ] ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'totals-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( DocumentTotals::class , $mixed[ 1 ] ) ;
    }
}
