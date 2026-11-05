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
use xyz\oihana\schema\business\documents\CompoundPriceSpecification as DocumentCompoundPriceSpecification;
use xyz\oihana\schema\business\documents\DeliveryNote;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\business\documents\UnitPriceSpecification as DocumentUnitPriceSpecification;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\QuantityOrigin;
use xyz\oihana\schema\enumerations\UnitOfSaleType;
use xyz\oihana\schema\statistics\ProductStatistics;

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
        $this->assertSame( 'additionalProperty'     , BusinessDocumentLine::ADDITIONAL_PROPERTY      );
        $this->assertSame( 'adjustments'            , BusinessDocumentLine::ADJUSTMENTS              );
        $this->assertSame( 'color'                  , BusinessDocumentLine::COLOR                    );
        $this->assertSame( 'costPrice'              , BusinessDocumentLine::COST_PRICE               );
        $this->assertSame( 'freeReason'             , BusinessDocumentLine::FREE_REASON              );
        $this->assertSame( 'includedInTotal'        , BusinessDocumentLine::INCLUDED_IN_TOTAL        );
        $this->assertSame( 'item'                   , BusinessDocumentLine::ITEM                     );
        $this->assertSame( 'position'               , BusinessDocumentLine::POSITION                 );
        $this->assertSame( 'price'                  , BusinessDocumentLine::PRICE                    );
        $this->assertSame( 'quantity'               , BusinessDocumentLine::QUANTITY                 );
        $this->assertSame( 'quantityOrigin'         , BusinessDocumentLine::QUANTITY_ORIGIN          );
        $this->assertSame( 'referencesDeliveryNote' , BusinessDocumentLine::REFERENCES_DELIVERY_NOTE );
        $this->assertSame( 'referencesOrder'        , BusinessDocumentLine::REFERENCES_ORDER         );
        $this->assertSame( 'section'                , BusinessDocumentLine::SECTION                  );
        $this->assertSame( 'subtotal'               , BusinessDocumentLine::SUBTOTAL                 );
        $this->assertSame( 'taxes'                  , BusinessDocumentLine::TAXES                    );
        $this->assertSame( 'technicalNote'          , BusinessDocumentLine::TECHNICAL_NOTE           );
        $this->assertSame( 'total'                  , BusinessDocumentLine::TOTAL                    );
        $this->assertSame( 'unit'                   , BusinessDocumentLine::UNIT                     );
        $this->assertSame( 'unitCostPrice'          , BusinessDocumentLine::UNIT_COST_PRICE          );
        $this->assertSame( 'unitPurchasePrice'      , BusinessDocumentLine::UNIT_PURCHASE_PRICE      );
        $this->assertSame( 'volume'                 , BusinessDocumentLine::VOLUME                   );
        $this->assertSame( 'weight'                 , BusinessDocumentLine::WEIGHT                   );

        $this->assertSame( Oihana::ADJUSTMENTS , BusinessDocumentLine::ADJUSTMENTS );
    }

    /**
     * A line and its invoice name the same references with the same words :
     * a reader learns the key once, and the constants of the two traits meet
     * in the umbrella trait without a conflict.
     */
    public function testTheReferencesShareTheirNamesWithTheInvoice(): void
    {
        $this->assertSame( Invoice::REFERENCES_DELIVERY_NOTE , BusinessDocumentLine::REFERENCES_DELIVERY_NOTE ) ;
        $this->assertSame( Invoice::REFERENCES_ORDER         , BusinessDocumentLine::REFERENCES_ORDER         ) ;
        $this->assertSame( Oihana::REFERENCES_DELIVERY_NOTE  , BusinessDocumentLine::REFERENCES_DELIVERY_NOTE ) ;
        $this->assertSame( Oihana::REFERENCES_ORDER          , BusinessDocumentLine::REFERENCES_ORDER         ) ;
    }

    public function testDefaults(): void
    {
        $line = new BusinessDocumentLine() ;

        $this->assertNull( $line->additionalProperty     ?? null );
        $this->assertNull( $line->adjustments            ?? null );
        $this->assertNull( $line->color                  ?? null );
        $this->assertNull( $line->costPrice              ?? null );
        $this->assertNull( $line->freeReason             ?? null );
        $this->assertNull( $line->includedInTotal        ?? null );
        $this->assertNull( $line->item                   ?? null );
        $this->assertNull( $line->position               ?? null );
        $this->assertNull( $line->price                  ?? null );
        $this->assertNull( $line->quantity               ?? null );
        $this->assertNull( $line->quantityOrigin         ?? null );
        $this->assertNull( $line->referencesDeliveryNote ?? null );
        $this->assertNull( $line->referencesOrder        ?? null );
        $this->assertNull( $line->section                ?? null );
        $this->assertNull( $line->subtotal               ?? null );
        $this->assertNull( $line->taxes                  ?? null );
        $this->assertNull( $line->technicalNote          ?? null );
        $this->assertNull( $line->total                  ?? null );
        $this->assertNull( $line->unit                   ?? null );
        $this->assertNull( $line->unitCostPrice          ?? null );
        $this->assertNull( $line->unitPurchasePrice      ?? null );
        $this->assertNull( $line->volume                 ?? null );
        $this->assertNull( $line->weight                 ?? null );
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
     * A line says nothing about where its quantity came from unless someone states
     * it — and silence must not be read as « a human chose this number ».
     *
     * @return void
     * @throws ReflectionException
     */
    public function testQuantityOriginIsSilentUnlessStated(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION => 100 ,
            BusinessDocumentLine::QUANTITY => 12  ,
        ]);

        $this->assertNull( $line->quantityOrigin ) ;
    }

    /**
     * The two states survive hydration, and the value travels as it was written —
     * a reader tells a computed quantity from a typed one by this and nothing else.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testQuantityOriginHydratesBothStates(): void
    {
        $computed = new BusinessDocumentLine
        ([
            BusinessDocumentLine::QUANTITY        => 0.076                     ,
            BusinessDocumentLine::QUANTITY_ORIGIN => QuantityOrigin::CALCULATED ,
        ]);

        $typed = new BusinessDocumentLine
        ([
            BusinessDocumentLine::QUANTITY        => 2.5                    ,
            BusinessDocumentLine::QUANTITY_ORIGIN => QuantityOrigin::ENTERED ,
        ]);

        $this->assertSame( QuantityOrigin::CALCULATED , $computed->quantityOrigin ) ;
        $this->assertSame( QuantityOrigin::ENTERED    , $typed->quantityOrigin    ) ;
    }

    /**
     * A bare key is left as read : the reference of a note or of an order is
     * often all a source gives about it.
     *
     * @throws ReflectionException
     */
    public function testTheReferencesKeepABareKey(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION                 => 1             ,
            BusinessDocumentLine::REFERENCES_DELIVERY_NOTE => 'DN-2026-0412' ,
            BusinessDocumentLine::REFERENCES_ORDER         => 'CDE-1148902'  ,
        ]);

        $this->assertSame( 'DN-2026-0412' , $line->referencesDeliveryNote ) ;
        $this->assertSame( 'CDE-1148902'  , $line->referencesOrder        ) ;
    }

    /**
     * The constructor assigns raw : a row naming the note and the order sits in
     * the properties until `Reflection::hydrate()` resolves it.
     *
     * @throws ReflectionException
     */
    public function testConstructorKeepsTheReferencesAsRawArrays(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::REFERENCES_DELIVERY_NOTE => [ 'id' => 'DN-2026-0412' ] ,
            BusinessDocumentLine::REFERENCES_ORDER         => [ 'id' => 'CDE-1148902'  ] ,
        ]);

        $this->assertIsArray( $line->referencesDeliveryNote ) ;
        $this->assertIsArray( $line->referencesOrder        ) ;
        $this->assertSame( 'DN-2026-0412' , $line->referencesDeliveryNote[ 'id' ] ) ;
        $this->assertSame( 'CDE-1148902'  , $line->referencesOrder[ 'id' ]        ) ;
    }

    /**
     * Each union names a single class, so a row resolves on its own : the note
     * into a `DeliveryNote`, the order into a `PurchaseOrder`.
     *
     * @throws ReflectionException
     */
    public function testReflectionResolvesTheDeliveryNoteAndTheOrder(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::REFERENCES_DELIVERY_NOTE => [ 'id' => 'DN-2026-0412' ] ,
                BusinessDocumentLine::REFERENCES_ORDER         => [ 'id' => 'CDE-1148902'  ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( DeliveryNote::class  , $line->referencesDeliveryNote ) ;
        $this->assertInstanceOf( PurchaseOrder::class , $line->referencesOrder        ) ;
        $this->assertSame( 'DN-2026-0412' , $line->referencesDeliveryNote->id ) ;
        $this->assertSame( 'CDE-1148902'  , $line->referencesOrder->id        ) ;
    }

    /**
     * A line that names no note — a quote line, an order line — serializes
     * without the keys, rather than with a `null` a reader could take for a
     * line delivered by nothing.
     *
     * @throws ReflectionException
     */
    public function testTheReferencesAreAbsentFromALineThatDoesNotStateThem(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION => 1 ,
            BusinessDocumentLine::QUANTITY => 5 ,
        ]);

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertArrayNotHasKey( BusinessDocumentLine::REFERENCES_DELIVERY_NOTE , $json ) ;
        $this->assertArrayNotHasKey( BusinessDocumentLine::REFERENCES_ORDER         , $json ) ;
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

    /**
     * A price folded into the printed one : the price the customer read, then
     * the list price and the discount it was reached from, marked unprinted.
     * Hydrated with the schema.org classes, the mark would be lost.
     *
     * @throws ReflectionException
     */
    public function testReflectionKeepsTheUnprintedComponents(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE =>
                [
                    'priceComponent' =>
                    [
                        [ 'price' => 36.0 , 'priceCurrency' => 'EUR' ] ,
                        [ 'price' => 40.0 , 'priceCurrency' => 'EUR' , DocumentUnitPriceSpecification::PRINTED => false ] ,
                        [ 'price' => 10.0 , 'unitCode' => 'P1' , DocumentUnitPriceSpecification::PRINTED => false ] ,
                    ] ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( DocumentCompoundPriceSpecification::class , $line->price ) ;
        $this->assertContainsOnlyInstancesOf( DocumentUnitPriceSpecification::class , $line->price->priceComponent ) ;

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertArrayNotHasKey( DocumentUnitPriceSpecification::PRINTED , $json[ 'price' ][ 'priceComponent' ][ 0 ] ) ;
        $this->assertFalse( $json[ 'price' ][ 'priceComponent' ][ 1 ][ DocumentUnitPriceSpecification::PRINTED ] ) ;
        $this->assertFalse( $json[ 'price' ][ 'priceComponent' ][ 2 ][ DocumentUnitPriceSpecification::PRINTED ] ) ;
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

    /**
     * The cost of a whole line and the cost of a body of trades are named alike :
     * `costPrice` is an amount wherever it is met, and the constants of the two
     * traits meet in the umbrella trait without a conflict.
     */
    public function testTheCostPriceSharesItsNameWithTheTradingMeasures(): void
    {
        $this->assertSame( ProductStatistics::COST_PRICE , BusinessDocumentLine::COST_PRICE ) ;
        $this->assertSame( Oihana::COST_PRICE            , BusinessDocumentLine::COST_PRICE ) ;
        $this->assertSame( Oihana::UNIT_COST_PRICE       , BusinessDocumentLine::UNIT_COST_PRICE ) ;
        $this->assertSame( Oihana::UNIT_PURCHASE_PRICE   , BusinessDocumentLine::UNIT_PURCHASE_PRICE ) ;
    }

    /**
     * The constructor assigns raw : the three costs sit as read until
     * `Reflection::hydrate()` resolves them.
     *
     * @throws ReflectionException
     */
    public function testConstructorKeepsTheCostsAsRawArrays(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::COST_PRICE          => [ 'value' => 1284.88 , 'currency' => 'EUR' ] ,
            BusinessDocumentLine::UNIT_COST_PRICE     => [ 'price' => 25.40 , 'priceCurrency' => 'EUR' , 'unitCode' => 'MTK' ] ,
            BusinessDocumentLine::UNIT_PURCHASE_PRICE => [ 'price' => 24.10 , 'priceCurrency' => 'EUR' , 'unitCode' => 'MTK' ] ,
        ]);

        $this->assertIsArray( $line->costPrice         ) ;
        $this->assertIsArray( $line->unitCostPrice     ) ;
        $this->assertIsArray( $line->unitPurchasePrice ) ;
        $this->assertSame( 1284.88 , $line->costPrice[ 'value' ] ) ;
    }

    /**
     * The costs are declared by {@see \xyz\oihana\schema\traits\HasLineCosts} :
     * a row resolves into the classes the trait names, the amount into a
     * `MonetaryAmount`, the two unit prices into `UnitPriceSpecification`s.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheCosts(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::COST_PRICE          => [ 'value' => 1284.88 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::UNIT_COST_PRICE     => [ 'price' => 25.40 , 'priceCurrency' => 'EUR' , 'unitCode' => 'MTK' ] ,
                BusinessDocumentLine::UNIT_PURCHASE_PRICE => [ 'price' => 24.10 , 'priceCurrency' => 'EUR' , 'unitCode' => 'MTK' ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( MonetaryAmount::class         , $line->costPrice         ) ;
        $this->assertInstanceOf( UnitPriceSpecification::class , $line->unitCostPrice     ) ;
        $this->assertInstanceOf( UnitPriceSpecification::class , $line->unitPurchasePrice ) ;

        $this->assertSame( 1284.88 , $line->costPrice->value             ) ;
        $this->assertSame( 'EUR'   , $line->costPrice->currency          ) ;
        $this->assertSame( 25.40   , $line->unitCostPrice->price         ) ;
        $this->assertSame( 'MTK'   , $line->unitCostPrice->unitCode      ) ;
        $this->assertSame( 24.10   , $line->unitPurchasePrice->price     ) ;
    }

    /**
     * A line that discloses no cost — none is known, or the reader may not see
     * it — serializes without the keys, rather than with a `null` or a zero a
     * reader could take for a whole margin.
     *
     * @throws ReflectionException
     */
    public function testTheCostsAreAbsentFromALineThatDoesNotStateThem(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION => 1 ,
            BusinessDocumentLine::SUBTOTAL => [ 'value' => 1525.00 , 'currency' => 'EUR' ] ,
        ]);

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertArrayNotHasKey( BusinessDocumentLine::COST_PRICE          , $json ) ;
        $this->assertArrayNotHasKey( BusinessDocumentLine::UNIT_COST_PRICE     , $json ) ;
        $this->assertArrayNotHasKey( BusinessDocumentLine::UNIT_PURCHASE_PRICE , $json ) ;
    }

    /**
     * A zero is a cost the source states — a fee that costs nothing to sell —
     * and it is kept : only an absent key says the cost is unknown.
     *
     * @throws ReflectionException
     */
    public function testAZeroCostIsKept(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::SUBTOTAL   => [ 'value' => 4.50 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::COST_PRICE => [ 'value' => 0    , 'currency' => 'EUR' ] ,
            ],
            BusinessDocumentLine::class
        );

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertArrayHasKey( BusinessDocumentLine::COST_PRICE , $json ) ;
        $this->assertEquals( 0 , $json[ BusinessDocumentLine::COST_PRICE ][ 'value' ] ) ;
    }
}
