<?php

namespace tests\xyz\oihana\schema\business\documents ;

use org\schema\DefinedTerm;
use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\CompoundPriceSpecification;
use org\schema\MonetaryAmount;
use org\schema\PropertyValue;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\UnitOfSaleType;

class BusinessDocumentLineTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new BusinessDocumentLine() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , BusinessDocumentLine::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'additionalProperty' , BusinessDocumentLine::ADDITIONAL_PROPERTY );
        $this->assertSame( 'adjustments'        , BusinessDocumentLine::ADJUSTMENTS         );
        $this->assertSame( 'color'              , BusinessDocumentLine::COLOR               );
        $this->assertSame( 'freeReason'         , BusinessDocumentLine::FREE_REASON         );
        $this->assertSame( 'includedInTotal'    , BusinessDocumentLine::INCLUDED_IN_TOTAL   );
        $this->assertSame( 'item'               , BusinessDocumentLine::ITEM                );
        $this->assertSame( 'position'           , BusinessDocumentLine::POSITION            );
        $this->assertSame( 'price'              , BusinessDocumentLine::PRICE               );
        $this->assertSame( 'quantity'           , BusinessDocumentLine::QUANTITY            );
        $this->assertSame( 'section'            , BusinessDocumentLine::SECTION             );
        $this->assertSame( 'subtotal'           , BusinessDocumentLine::SUBTOTAL            );
        $this->assertSame( 'taxes'              , BusinessDocumentLine::TAXES               );
        $this->assertSame( 'technicalNote'      , BusinessDocumentLine::TECHNICAL_NOTE      );
        $this->assertSame( 'total'              , BusinessDocumentLine::TOTAL               );
        $this->assertSame( 'unit'               , BusinessDocumentLine::UNIT                );
        $this->assertSame( 'volume'             , BusinessDocumentLine::VOLUME              );
        $this->assertSame( 'weight'             , BusinessDocumentLine::WEIGHT              );

        $this->assertSame( Oihana::ADJUSTMENTS , BusinessDocumentLine::ADJUSTMENTS );
    }

    public function testDefaults(): void
    {
        $line = new BusinessDocumentLine() ;

        $this->assertNull( $line->additionalProperty ?? null );
        $this->assertNull( $line->adjustments        ?? null );
        $this->assertNull( $line->color              ?? null );
        $this->assertNull( $line->freeReason         ?? null );
        $this->assertNull( $line->includedInTotal    ?? null );
        $this->assertNull( $line->item               ?? null );
        $this->assertNull( $line->position           ?? null );
        $this->assertNull( $line->price              ?? null );
        $this->assertNull( $line->quantity           ?? null );
        $this->assertNull( $line->section            ?? null );
        $this->assertNull( $line->subtotal           ?? null );
        $this->assertNull( $line->taxes              ?? null );
        $this->assertNull( $line->technicalNote      ?? null );
        $this->assertNull( $line->total              ?? null );
        $this->assertNull( $line->unit               ?? null );
        $this->assertNull( $line->volume             ?? null );
        $this->assertNull( $line->weight             ?? null );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorHasColor(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::COLOR => '#ff0000' ,
        ]);

        $this->assertEquals( '#ff0000' , $line->color ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorHydratesScalarProperties(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION       => 1 ,
            BusinessDocumentLine::QUANTITY       => 5 ,
            BusinessDocumentLine::TECHNICAL_NOTE => 'ne pas remettre en stock' ,
            BusinessDocumentLine::UNIT           => UnitOfSaleType::UNIT ,
        ]);

        $this->assertSame( 1 , $line->position ) ;
        $this->assertSame( 5 , $line->quantity ) ;
        $this->assertSame( 'ne pas remettre en stock' , $line->technicalNote ) ;
        $this->assertSame( UnitOfSaleType::UNIT , $line->unit ) ;
    }

    /**
     * The technical note is a sibling of the inherited `description`, not a
     * replacement : the two must survive side by side on the same line.
     * @return void
     * @throws ReflectionException
     */
    public function testTechnicalNoteAndDescriptionCoexist(): void
    {
        $line = new BusinessDocumentLine
        ([
            'description'                        => 'Palette de 12 bouteilles' ,
            BusinessDocumentLine::TECHNICAL_NOTE => 'reprendre les 3 colis'    ,
        ]);

        $this->assertSame( 'Palette de 12 bouteilles' , $line->description   ) ;
        $this->assertSame( 'reprendre les 3 colis'    , $line->technicalNote ) ;
    }

    /**
     * An absent flag means the line counts : a document written before the
     * property existed must keep summing exactly as it did.
     * @return void
     * @throws ReflectionException
     */
    public function testIncludedInTotalIsAbsentOnAnOrdinaryLine(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION => 325 ,
            BusinessDocumentLine::QUANTITY => 42  ,
        ]);

        $this->assertNull( $line->includedInTotal ) ;
    }

    /**
     * Only a line left out of the totals says so, and it says so with `false` —
     * the quote that offers two floorings and bills one.
     * @return void
     * @throws ReflectionException
     */
    public function testIncludedInTotalHydratesFalse(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION          => 400   ,
            BusinessDocumentLine::INCLUDED_IN_TOTAL => false ,
        ]);

        $this->assertFalse( $line->includedInTotal ) ;
    }

    /**
     * The heading is shared by several lines, the description names one item :
     * the two must survive side by side.
     * @return void
     * @throws ReflectionException
     */
    public function testSectionAndDescriptionCoexist(): void
    {
        $line = new BusinessDocumentLine
        ([
            'description'                 => 'Parquet chêne fumé léger' ,
            BusinessDocumentLine::SECTION => 'PARQUET CHENE FUME'       ,
        ]);

        $this->assertSame( 'Parquet chêne fumé léger' , $line->description ) ;
        $this->assertSame( 'PARQUET CHENE FUME'       , $line->section     ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorKeepsFreeReasonAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::FREE_REASON => [ Oihana::ID => 'DEF' , Oihana::NAME => 'Défaut' ] ,
        ]);

        $this->assertIsArray( $line->freeReason ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorKeepsPriceAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::PRICE => [ 'value' => 20 , 'currency' => 'EUR' ] ,
        ]);

        $this->assertIsArray( $line->price ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorKeepsQuantityAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::QUANTITY => [ 'value' => 5 , 'unitCode' => 'C62' ] ,
        ]);

        $this->assertIsArray( $line->quantity ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorKeepsAdditionalPropertyAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::ADDITIONAL_PROPERTY =>
            [
                [ 'propertyID' => 'lotNumber' , 'value' => 'LOT-2026-08' ] ,
            ] ,
        ]);

        $this->assertIsArray( $line->additionalProperty ) ;
        $this->assertIsArray( $line->additionalProperty[ 0 ] ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTaxesAndAdjustmentsIntoObjects(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::TAXES       => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocumentLine::ADJUSTMENTS => [ [ 'type' => 'discount' , 'percentage' => 10.0 ] ] ,
                BusinessDocumentLine::SUBTOTAL    => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::TOTAL       => [ 'value' => 108 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::QUANTITY    => [ 'value' => 5 , 'unitCode' => 'C62' ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( TaxDetail::class , $line->taxes[ 0 ] ) ;
        $this->assertSame( 'VAT' , $line->taxes[ 0 ]->category ) ;

        $this->assertInstanceOf( Adjustment::class , $line->adjustments[ 0 ] ) ;
        $this->assertSame( 10.0 , $line->adjustments[ 0 ]->percentage ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $line->subtotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $line->total ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->quantity ) ;
        $this->assertSame( 5 , $line->quantity->value ) ;
    }

    /**
     * The term is designated by its code and its label — the pair the class
     * documents as what freezes the reason.
     * @throws ReflectionException
     */
    public function testReflectionHydratesFreeReasonIntoADefinedTerm(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::FREE_REASON =>
                [
                    Oihana::ID   => 'DEF'    ,
                    Oihana::NAME => 'Défaut' ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( DefinedTerm::class , $line->freeReason ) ;
        $this->assertSame( 'DEF'    , $line->freeReason->id   ) ;
        $this->assertSame( 'Défaut' , $line->freeReason->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheAdditionalProperty(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::ADDITIONAL_PROPERTY =>
                [
                    [ 'propertyID' => 'lotNumber'    , 'value' => 'LOT-2026-08' ] ,
                    [ 'propertyID' => 'serialNumber' , 'value' => 'SN-0042'     ] ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertIsArray( $line->additionalProperty ) ;
        $this->assertCount( 2 , $line->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $line->additionalProperty ) ;

        $this->assertSame( 'lotNumber'    , $line->additionalProperty[ 0 ]->propertyID ) ;
        $this->assertSame( 'LOT-2026-08'  , $line->additionalProperty[ 0 ]->value      ) ;
        $this->assertSame( 'serialNumber' , $line->additionalProperty[ 1 ]->propertyID ) ;
        $this->assertSame( 'SN-0042'      , $line->additionalProperty[ 1 ]->value      ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesPriceIntoCompoundPriceSpecification(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE =>
                [
                    'price'         => 20.0  ,
                    'priceCurrency' => 'EUR' ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( CompoundPriceSpecification::class , $line->price ) ;
        $this->assertSame( 20.0  , $line->price->price         ) ;
        $this->assertSame( 'EUR' , $line->price->priceCurrency ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesThePriceComponents(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE =>
                [
                    'price'          => 22.5  ,
                    'priceCurrency'  => 'EUR' ,
                    'priceComponent' =>
                    [
                        [ 'name' => 'base'    , 'price' => 20.0 , 'priceCurrency' => 'EUR' ] ,
                        [ 'name' => 'ecoFee'  , 'price' => 2.5  , 'priceCurrency' => 'EUR' ] ,
                    ] ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( CompoundPriceSpecification::class , $line->price ) ;
        $this->assertIsArray( $line->price->priceComponent ) ;
        $this->assertCount( 2 , $line->price->priceComponent ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $line->price->priceComponent[ 0 ] ) ;
        $this->assertSame( 'base' , $line->price->priceComponent[ 0 ]->name  ) ;
        $this->assertSame( 20.0   , $line->price->priceComponent[ 0 ]->price ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $line->price->priceComponent[ 1 ] ) ;
        $this->assertSame( 'ecoFee' , $line->price->priceComponent[ 1 ]->name  ) ;
        $this->assertSame( 2.5      , $line->price->priceComponent[ 1 ]->price ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratedPriceKeepsItsTypeOnSerialization(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE => [ 'price' => 20.0 , 'priceCurrency' => 'EUR' ] ,
            ],
            BusinessDocumentLine::class
        );

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertSame( 'CompoundPriceSpecification' , $json[ 'price' ][ '@type' ] ) ;
        $this->assertEquals( 20.0 , $json[ 'price' ][ 'price'         ] ) ;
        $this->assertSame( 'EUR'  , $json[ 'price' ][ 'priceCurrency' ] ) ;
    }

    // ---- HasPhysicalMeasures

    /**
     * What a line weighs and what it occupies : the quantity by what the unit
     * it is counted in weighs and occupies. A plain number carries both when
     * the unit is implicit, and the sum over the lines is the document's own.
     *
     * @throws ReflectionException
     */
    public function testTheLineCarriesItsWeightAndItsVolumeAsPlainNumbers(): void
    {
        $lines =
        [
            new BusinessDocumentLine
            ([
                BusinessDocumentLine::QUANTITY => 84 ,
                BusinessDocumentLine::WEIGHT   => 537.6 ,  // 84 × 6.4
                BusinessDocumentLine::VOLUME   => 1.176 ,  // 84 × 0.014
            ]) ,
            new BusinessDocumentLine
            ([
                BusinessDocumentLine::QUANTITY => 1467 ,
                BusinessDocumentLine::WEIGHT   => 1193.775 ,
                BusinessDocumentLine::VOLUME   => 2.236 ,
            ]) ,
        ];

        $this->assertSame( 537.6    , $lines[ 0 ]->weight ) ;
        $this->assertSame( 1.176    , $lines[ 0 ]->volume ) ;
        $this->assertSame( 1193.775 , $lines[ 1 ]->weight ) ;
        $this->assertSame( 2.236    , $lines[ 1 ]->volume ) ;

        $this->assertSame( 1731.375 , array_sum( array_map( fn( $line ) => $line->weight , $lines ) ) ) ;
        $this->assertSame( 3.412    , array_sum( array_map( fn( $line ) => $line->volume , $lines ) ) ) ;
    }

    /**
     * A measure that states its unit comes back typed, so a consumer reads the
     * unit instead of assuming one.
     *
     * 🔑 The two properties are declared by {@see \xyz\oihana\schema\traits\HasPhysicalMeasures},
     * so this is also what proves the `#[HydrateAs]` carried by a **trait**
     * property is honored by `Reflection::hydrate()` — the whole point of
     * declaring the pair once rather than once per line class.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesMeasuresThatStateTheirUnit(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::WEIGHT => [ 'value' => 537.6 , 'unitCode' => 'KGM' ] ,
                BusinessDocumentLine::VOLUME => [ 'value' => 1.176 , 'unitCode' => 'MTQ' ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $line->weight ) ;
        $this->assertSame( 537.6 , $line->weight->value    ) ;
        $this->assertSame( 'KGM' , $line->weight->unitCode ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->volume ) ;
        $this->assertSame( 1.176 , $line->volume->value    ) ;
        $this->assertSame( 'MTQ' , $line->volume->unitCode ) ;
    }

    /**
     * The two are independent : a line stating only one of them leaves the
     * other absent, rather than reading as a zero.
     *
     * @throws ReflectionException
     */
    public function testAMeasureLeftOutStaysAbsent(): void
    {
        $line = new Reflection()->hydrate
        (
            [ BusinessDocumentLine::WEIGHT => 537.6 ],
            BusinessDocumentLine::class
        );

        $this->assertSame( 537.6 , $line->weight ) ;
        $this->assertNull( $line->volume ?? null ) ;
    }
}
