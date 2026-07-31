<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\traits\thesaurus\DeliveryMethodTermTrait;
use xyz\oihana\schema\enumerations\ShippingChargeTiming;
use xyz\oihana\schema\products\TaxRate;
use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

class DeliveryMethodTermTest extends TestCase
{
    public function testDefaults(): void
    {
        $term = new DeliveryMethodTerm();

        $this->assertNull( $term->chargeTiming           ?? null );
        $this->assertNull( $term->freeShippingThreshold ?? null );
        $this->assertNull( $term->shippingRate           ?? null );
        $this->assertNull( $term->vat                    ?? null );
    }

    public function testIsThesaurusTerm(): void
    {
        $term = new DeliveryMethodTerm();

        $this->assertInstanceOf( ThesaurusTerm::class , $term );
        $this->assertInstanceOf( DefinedTerm::class    , $term );
    }

    public function testUsesDeliveryMethodTermTrait(): void
    {
        $this->assertContains( DeliveryMethodTermTrait::class , class_uses( DeliveryMethodTerm::class ) );
    }

    public function testContextInheritedFromThesaurusTerm(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , DeliveryMethodTerm::CONTEXT );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'chargeTiming'          , DeliveryMethodTerm::CHARGE_TIMING );
        $this->assertSame( 'freeShippingThreshold' , DeliveryMethodTerm::FREE_SHIPPING_THRESHOLD );
        $this->assertSame( 'shippingRate'           , DeliveryMethodTerm::SHIPPING_RATE );
        $this->assertSame( 'vat'                    , DeliveryMethodTerm::VAT );
    }

    /**
     * The constructor copies scalar fields verbatim, including the inherited
     * `id`/`name`/`identifier` and the four delivery-specific properties.
     */
    public function testConstructorCopiesScalarProperties(): void
    {
        $term = new DeliveryMethodTerm
        ([
            'id'                                        => 'F11' ,
            'name'                                      => 'Free above 1000, otherwise 39' ,
            'identifier'                                => '59483' ,
            DeliveryMethodTerm::SHIPPING_RATE           => 39 ,
            DeliveryMethodTerm::FREE_SHIPPING_THRESHOLD => 1000 ,
            DeliveryMethodTerm::CHARGE_TIMING           => ShippingChargeTiming::AT_ORDER ,
        ]);

        $this->assertSame( 'F11'                             , $term->id );
        $this->assertSame( 'Free above 1000, otherwise 39'    , $term->name );
        $this->assertSame( '59483'                            , $term->identifier );
        $this->assertSame( 39                                 , $term->shippingRate );
        $this->assertSame( 1000                               , $term->freeShippingThreshold );
        $this->assertSame( ShippingChargeTiming::AT_ORDER     , $term->chargeTiming );
    }

    /**
     * `freeShippingThreshold === null` reads as *never free* — the constructor
     * leaves an omitted threshold uninitialized rather than defaulting it, so a
     * caller must check with the null-coalescing operator.
     */
    public function testFreeShippingThresholdDefaultsToNullWhenOmitted(): void
    {
        $term = new DeliveryMethodTerm([ 'name' => 'Flat rate' , DeliveryMethodTerm::SHIPPING_RATE => 9.9 ]);

        $this->assertSame( 9.9  , $term->shippingRate );
        $this->assertNull( $term->freeShippingThreshold ?? null );
    }

    /**
     * The constructor performs a raw assignment : a scalar `vat` (raw tax code)
     * is stored as-is, with no attempt to resolve it against a `TaxRate`.
     */
    public function testConstructorKeepsVatRawWhenScalar(): void
    {
        $term = new DeliveryMethodTerm([ 'name' => 'Carrier' , DeliveryMethodTerm::VAT => 'FR-20' ]);

        $this->assertSame( 'FR-20' , $term->vat );
    }

    /**
     * The constructor performs a raw assignment : an array `vat` is stored
     * as-is (not hydrated into a `TaxRate`), unlike the Reflection hydration path.
     */
    public function testConstructorKeepsVatAsRawArray(): void
    {
        $term = new DeliveryMethodTerm
        ([
            'name'                  => 'Carrier' ,
            DeliveryMethodTerm::VAT => [ 'price' => 20 , 'priceCurrency' => 'EUR' ] ,
        ]);

        $this->assertIsArray( $term->vat );
        $this->assertSame( 20 , $term->vat[ 'price' ] );
    }

    /**
     * Through the reflection-based hydration path, a raw scalar `vat` (tax code
     * as read from the source) is kept unchanged.
     *
     * @throws ReflectionException
     */
    public function testReflectionKeepsVatRawWhenScalar(): void
    {
        $term = ( new Reflection() )->hydrate
        (
            [ 'name' => 'Carrier' , DeliveryMethodTerm::VAT => 'FR-20' ] ,
            DeliveryMethodTerm::class
        );

        $this->assertSame( 'FR-20' , $term->vat );
    }

    /**
     * Through the reflection-based hydration path, an associative array `vat`
     * (the joined tax reference row) is resolved into a {@see TaxRate} instance —
     * the same shape {@see \xyz\oihana\schema\organizations\Company::$vat} follows.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesVatAsTaxRate(): void
    {
        $term = ( new Reflection() )->hydrate
        (
            [
                'name'                  => 'Carrier' ,
                DeliveryMethodTerm::VAT =>
                [
                    'price'         => 20 ,
                    'priceCurrency' => 'EUR' ,
                ],
            ],
            DeliveryMethodTerm::class
        );

        $this->assertInstanceOf( TaxRate::class , $term->vat );
        $this->assertSame( 20      , $term->vat->price );
        $this->assertSame( 'EUR'   , $term->vat->priceCurrency );
    }

    /**
     * Through the reflection-based hydration path, `shippingRate` and
     * `freeShippingThreshold` stay plain scalars — no value-object wrapping,
     * so a term hydrates directly from a flat table row.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesAmountsAsScalars(): void
    {
        $term = ( new Reflection() )->hydrate
        (
            [
                'name'                                      => 'Free above 1000, otherwise 39' ,
                DeliveryMethodTerm::SHIPPING_RATE           => 39 ,
                DeliveryMethodTerm::FREE_SHIPPING_THRESHOLD => 1000 ,
            ],
            DeliveryMethodTerm::class
        );

        $this->assertIsInt( $term->shippingRate );
        $this->assertSame( 39   , $term->shippingRate );
        $this->assertIsInt( $term->freeShippingThreshold );
        $this->assertSame( 1000 , $term->freeShippingThreshold );
    }

    public function testShippingRateAndThresholdAssignment(): void
    {
        $term = new DeliveryMethodTerm();

        $term->shippingRate           = 4.9 ;
        $term->freeShippingThreshold  = 50 ;

        $this->assertSame( 4.9 , $term->shippingRate );
        $this->assertSame( 50  , $term->freeShippingThreshold );
    }

    /**
     * `chargeTiming` accepts either a {@see ShippingChargeTiming} constant or a
     * plain free-text label — the type is not restricted to the enumeration.
     */
    public function testChargeTimingAssignment(): void
    {
        $term = new DeliveryMethodTerm();

        $term->chargeTiming = ShippingChargeTiming::AT_DELIVERY ;
        $this->assertSame( ShippingChargeTiming::AT_DELIVERY , $term->chargeTiming );

        $term->chargeTiming = 'on dispatch' ;
        $this->assertSame( 'on dispatch' , $term->chargeTiming );
    }

    /**
     * Through the reflection-based hydration path, `chargeTiming` stays a
     * plain scalar — no value-object wrapping.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesChargeTimingAsScalar(): void
    {
        $term = ( new Reflection() )->hydrate
        (
            [
                'name'                             => 'Free above 1000, otherwise 39' ,
                DeliveryMethodTerm::CHARGE_TIMING => ShippingChargeTiming::AT_DELIVERY ,
            ],
            DeliveryMethodTerm::class
        );

        $this->assertSame( ShippingChargeTiming::AT_DELIVERY , $term->chargeTiming );
    }
}
