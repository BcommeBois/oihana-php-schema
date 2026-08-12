<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\QuantitativeValue;
use org\schema\SomeProducts;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\ProductAdditionalProperty;
use xyz\oihana\schema\enumerations\FeeUnresolvedReason;
use xyz\oihana\schema\enumerations\UnitOfSaleType;
use xyz\oihana\schema\products\FeeSpecification;
use xyz\oihana\schema\products\PhysicalQuantity;
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\products\StockLevel;

class ProductTest extends TestCase
{
    public function testIsSomeProducts(): void
    {
        $this->assertInstanceOf( SomeProducts::class , new Product() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Product::CONTEXT );
    }

    // ---- __set + setAdditionalProperties

    public function testMagicSetHandlesAdditionalProperties(): void
    {
        $product = new Product() ;

        $product->essence = 'oak' ;

        $this->assertCount( 1 , $product->additionalProperty ) ;
        $this->assertSame( ProductAdditionalProperty::ESSENCE , $product->additionalProperty[0]->propertyID ) ;
        $this->assertSame( 'oak' , $product->additionalProperty[0]->value ) ;
    }

    public function testMagicSetCastsTheGrainPropertyToBoolean(): void
    {
        $product = new Product() ;

        $product->grain = '1' ;

        $this->assertTrue( $product->additionalProperty[0]->value ) ;
    }

    public function testMagicSetSilentlyIgnoresUnknownProperties(): void
    {
        $product = new Product() ;

        $product->unknownProperty = 'value' ;

        $this->assertNull( $product->additionalProperty ) ;
        $this->assertNull( $product->eligibleQuantity ) ;
    }

    public function testSetAdditionalPropertiesRejectsUnknownProperty(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setAdditionalProperties( 'unknownProperty' , 'value' ) ) ;
    }

    public function testSetAdditionalPropertiesNullifiesEmptyStrings(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setAdditionalProperties( ProductAdditionalProperty::ESSENCE , '' ) ) ;
        $this->assertNull( $product->additionalProperty ) ;
    }

    // ---- setEligibleQuantityProperty

    /**
     * @throws ReflectionException
     */
    public function testEligibleQuantityBuildsTheUnitLevel(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode = 'MTK' ;

        $this->assertInstanceOf( QuantitativeValue::class , $product->eligibleQuantity ) ;
        $this->assertSame( UnitOfSaleType::UNIT , $product->eligibleQuantity->additionalType ) ;
        $this->assertSame( 'MTK'          , $product->eligibleQuantity->unitCode ) ;
        $this->assertSame( 'Square Meter' , $product->eligibleQuantity->unitText ) ;
        $this->assertSame( 1.0            , $product->eligibleQuantity->value    ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testEligibleQuantityChainsUnitPackageAndPallet(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;
        $product->eligiblePalletQuantityCode   = 'PF'  ;
        $product->eligiblePalletQuantityValue  = 48    ;

        $unit = $product->eligibleQuantity ;

        $this->assertSame( UnitOfSaleType::UNIT , $unit->additionalType ) ;

        $package = $unit->valueReference ;

        $this->assertInstanceOf( QuantitativeValue::class , $package ) ;
        $this->assertSame( UnitOfSaleType::PACKAGE , $package->additionalType ) ;
        $this->assertSame( 12.0 , $package->value ) ;

        $pallet = $package->valueReference ;

        $this->assertInstanceOf( QuantitativeValue::class , $pallet ) ;
        $this->assertSame( UnitOfSaleType::PARCEL , $pallet->additionalType ) ;
        $this->assertSame( 48.0 , $pallet->value ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testEligibleQuantityIgnoresAValueWithoutAnyUnitCode(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setEligibleQuantityProperty( Oihana::ELIGIBLE_PACKAGE_QUANTITY_VALUE , 12 ) ) ;
        $this->assertNull( $product->eligibleQuantity ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testEligibleQuantityRejectsAnUnknownProperty(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setEligibleQuantityProperty( 'unknownProperty' , 12 ) ) ;
    }

    // ---- resolveUnitCode

    /**
     * @throws ReflectionException
     */
    public function testResolveUnitCodeCanBeOverridenToMapProprietaryCodes(): void
    {
        $product = new class extends Product
        {
            protected function resolveUnitCode( mixed $value ) :?string
            {
                return $value === 'M2' ? 'MTK' : ( isset( $value ) ? (string) $value : null ) ;
            }
        };

        $product->eligibleUnitQuantityCode = 'M2' ;

        $this->assertSame( 'MTK' , $product->eligibleQuantity->unitCode ) ;
    }

    // ---- findEligibleQuantityByType

    /**
     * @throws ReflectionException
     */
    public function testFindEligibleQuantityByType(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;

        $package = $product->findEligibleQuantityByType( UnitOfSaleType::PACKAGE ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $package ) ;
        $this->assertEquals( 12 , $package->value ) ;

        $this->assertNull( $product->findEligibleQuantityByType( UnitOfSaleType::PARCEL ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFindEligibleQuantityByTypeWithoutEligibleQuantity(): void
    {
        $product = new Product() ;

        $this->assertNull( $product->findEligibleQuantityByType( UnitOfSaleType::UNIT ) ) ;
    }

    // ---- getUnitOfSaleConversionFactor

    /**
     * @throws ReflectionException
     */
    public function testConversionFactorDefaultsToOne(): void
    {
        $product = new Product() ;

        $this->assertSame( 1.0 , $product->getUnitOfSaleConversionFactor() ) ;

        $product->unitOfSale = UnitOfSaleType::UNIT ;

        $this->assertSame( 1.0 , $product->getUnitOfSaleConversionFactor() ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConversionFactorForThePackageLevel(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;
        $product->unitOfSale                   = UnitOfSaleType::PACKAGE ;

        $this->assertSame( 12.0 , $product->getUnitOfSaleConversionFactor() ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConversionFactorForThePalletLevelMultipliesTheLevels(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;
        $product->eligiblePalletQuantityCode   = 'PF'  ;
        $product->eligiblePalletQuantityValue  = 48    ;
        $product->unitOfSale                   = UnitOfSaleType::PARCEL ;

        $this->assertSame( 576.0 , $product->getUnitOfSaleConversionFactor() ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConversionFactorAcceptsNestedArrayReferences(): void
    {
        $product = new Product() ;

        $product->unitOfSale       = UnitOfSaleType::PACKAGE ;
        $product->eligibleQuantity = new QuantitativeValue
        ([
            'additionalType' => UnitOfSaleType::UNIT ,
            'value'          => 1 ,
            'valueReference' =>
            [
                'additionalType' => UnitOfSaleType::PACKAGE ,
                'value'          => 10
            ]
        ]) ;

        $this->assertSame( 10.0 , $product->getUnitOfSaleConversionFactor() ) ;
    }

    // ---- getInventoryLevelInUnitOfSale

    /**
     * @throws ReflectionException
     */
    public function testInventoryLevelInUnitOfSale(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;
        $product->unitOfSale                   = UnitOfSaleType::PACKAGE ;

        $level = new StockLevel([ 'value' => 24 ]) ;

        $this->assertSame( 2.0 , $product->getInventoryLevelInUnitOfSale( $level ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testInventoryLevelInUnitOfSaleWithoutStockLevel(): void
    {
        $product = new Product() ;

        $this->assertNull( $product->getInventoryLevelInUnitOfSale( null ) ) ;
        $this->assertNull( $product->getInventoryLevelInUnitOfSale( new StockLevel() ) ) ;
    }

    public function testConstructorKeepsEligibleQuantityAsRawArray(): void
    {
        $product = new Product([ 'eligibleQuantity' => [ 'value' => 12 , 'unitCode' => 'C62' ] ]) ;

        $this->assertIsArray( $product->eligibleQuantity ) ;
        $this->assertSame( 12 , $product->eligibleQuantity[ 'value' ] ) ;
    }

    /**
     * Only a scalar names a unit. An array used to be cast to the literal string
     * `"Array"` — a silent corruption — and an object raised a fatal Error.
     *
     * @throws ReflectionException
     */
    public function testResolveUnitCodeRejectsNonScalarValues(): void
    {
        $product = new Product() ;
        $resolve = fn( mixed $value ) : ?string => ( fn() => $this->resolveUnitCode( $value ) )
                 ->call( $product ) ;

        $this->assertSame( 'KGM' , $resolve( 'KGM' ) ) ;
        $this->assertSame( '42'  , $resolve( 42 ) ) ;

        $this->assertNull( $resolve( [ 'KGM' ] ) ) ;
        $this->assertNull( $resolve( new QuantitativeValue() ) ) ;
        $this->assertNull( $resolve( null ) ) ;
    }

    /**
     * A `StockLevel` whose `value` never got hydrated holds the raw shape the base
     * returned. Dividing that used to raise "Unsupported operand types".
     *
     * @throws ReflectionException
     */
    public function testInventoryLevelInUnitOfSaleIgnoresANonNumericValue(): void
    {
        $product = new Product() ;

        $numeric = new StockLevel() ;
        $numeric->value = '12' ;
        $this->assertSame( 12.0 , $product->getInventoryLevelInUnitOfSale( $numeric ) ) ;

        foreach ( [ [ 1 , 2 ] , 'abc' , new QuantitativeValue() ] as $raw )
        {
            $level = new StockLevel() ;
            $level->value = $raw ;

            $this->assertNull
            (
                $product->getInventoryLevelInUnitOfSale( $level ) ,
                sprintf( 'A %s value should read as unknown, not divide.' , get_debug_type( $raw ) )
            ) ;
        }
    }

    // ---- fees

    /**
     * A list, and every entry typed : several kinds of fee may sit on one
     * product, and they all read the same way.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesEveryFeeOfTheList(): void
    {
        $product = new Reflection()->hydrate
        (
            [
                Oihana::FEES =>
                [
                    [ Oihana::PRICE => 0.35 , Oihana::UNIT_CODE => 'MTK' ] ,
                    [ Oihana::UNIT_CODE => 'C62' , Oihana::UNRESOLVED_REASON => FeeUnresolvedReason::MISSING_FEE_RATE ] ,
                ]
            ],
            Product::class
        );

        $this->assertIsArray( $product->fees ) ;
        $this->assertContainsOnlyInstancesOf( FeeSpecification::class , $product->fees ) ;

        $this->assertSame( 0.35 , $product->fees[ 0 ]->price ) ;

        $this->assertNull( $product->fees[ 1 ]->price ?? null ) ;
        $this->assertSame( FeeUnresolvedReason::MISSING_FEE_RATE , $product->fees[ 1 ]->unresolvedReason ) ;
    }

    public function testFeesAreAbsentByDefault(): void
    {
        $this->assertNull( new Product()->fees ?? null ) ;
    }

    // ---- the packaging chain carries its weight

    /**
     * Every level the chain builds is a `PhysicalQuantity`, so a weight has a
     * node to sit on wherever it is stated.
     *
     * @throws ReflectionException
     */
    public function testEligibleQuantityBuildsPhysicalQuantities(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode     = 'MTK' ;
        $product->eligiblePackageQuantityCode  = 'PK'  ;
        $product->eligiblePackageQuantityValue = 12    ;
        $product->eligiblePalletQuantityCode   = 'PF'  ;
        $product->eligiblePalletQuantityValue  = 48    ;

        $unit = $product->eligibleQuantity ;

        $this->assertInstanceOf( PhysicalQuantity::class , $unit ) ;
        $this->assertInstanceOf( PhysicalQuantity::class , $unit->valueReference ) ;
        $this->assertInstanceOf( PhysicalQuantity::class , $unit->valueReference->valueReference ) ;
    }

    /**
     * Hydration types the chain down every level, so a weight is read the same
     * way wherever it sits — never `->weight` on one level and `['weight']` on
     * the next, on a structure whose point is the ratio between two of them.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesEveryLevelAsAPhysicalQuantity(): void
    {
        $product = new Reflection()->hydrate( $this->chainWithWeights() , Product::class ) ;

        $unit = $product->eligibleQuantity ;

        $this->assertInstanceOf( PhysicalQuantity::class , $unit ) ;
        $this->assertSame( 10.99 , $unit->weight ) ;

        $package = $unit->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 0.0312 , $package->volume ) ;
    }

    /**
     * The typed way to read any level is `findEligibleQuantityByType()`, which
     * rebuilds the node : it must hand back the weight and the volume, not
     * only the quantity — the deeper levels reach it as raw arrays.
     *
     * @throws ReflectionException
     */
    public function testFindEligibleQuantityByTypeCarriesTheWeightOfEveryLevel(): void
    {
        $product = new Reflection()->hydrate( $this->chainWithWeights() , Product::class ) ;

        $unit = $product->findEligibleQuantityByType( UnitOfSaleType::UNIT ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $unit ) ;
        $this->assertSame( 10.99 , $unit->weight ) ;

        $package = $product->findEligibleQuantityByType( UnitOfSaleType::PACKAGE ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 0.0312 , $package->volume ) ;

        // the ratio between two levels restates the packaging chain
        $this->assertEqualsWithDelta( 1.403 , $package->weight / $unit->weight , 0.001 ) ;
    }

    /**
     * The weights ride along without disturbing what the chain already
     * answered : the conversion factor is the one measured before they existed.
     *
     * @throws ReflectionException
     */
    public function testWeightsLeaveTheConversionFactorUntouched(): void
    {
        $product = new Reflection()->hydrate( $this->chainWithWeights() , Product::class ) ;

        $product->unitOfSale = UnitOfSaleType::PACKAGE ;

        $this->assertSame( 1.403 , $product->getUnitOfSaleConversionFactor() ) ;
    }

    /**
     * A two-level chain whose every node states what it weighs — the unit
     * level in square meters, the package level as the parcel it ships in.
     *
     * @return array<string,mixed>
     */
    private function chainWithWeights() :array
    {
        return
        [
            Oihana::ELIGIBLE_QUANTITY =>
            [
                Oihana::ADDITIONAL_TYPE => UnitOfSaleType::UNIT ,
                Oihana::VALUE           => 1 ,
                Oihana::UNIT_CODE       => 'MTK' ,
                Oihana::WEIGHT          => 10.99 ,
                Oihana::VALUE_REFERENCE =>
                [
                    Oihana::ADDITIONAL_TYPE => UnitOfSaleType::PACKAGE ,
                    Oihana::VALUE           => 1.403 ,
                    Oihana::UNIT_CODE       => 'PK' ,
                    Oihana::WEIGHT          => 15.419 ,
                    Oihana::VOLUME          => 0.0312 ,
                ]
            ]
        ];
    }
}
