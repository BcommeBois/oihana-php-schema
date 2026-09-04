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
use xyz\oihana\schema\products\ApplicableResource;
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\products\StockLevel;

use function xyz\oihana\schema\helpers\hydrate\hydrateApplicableResource;

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

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testSetAdditionalPropertiesRejectsUnknownProperty(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setAdditionalProperties( 'unknownProperty' , 'value' ) ) ;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testSetAdditionalPropertiesNullifiesEmptyStrings(): void
    {
        $product = new Product() ;

        $this->assertFalse( $product->setAdditionalProperties( ProductAdditionalProperty::ESSENCE , '' ) ) ;
        $this->assertNull( $product->additionalProperty ) ;
    }

    // ---- setEligibleQuantityProperty

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
     * @return void
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

    // ---- each level carries what it weighs

    /**
     * The three levels each receive their own measures, on the node they
     * describe — a product sold by the box does not weigh what the same
     * product weighs by the piece.
     *
     * @throws ReflectionException
     */
    public function testEachLevelOfTheChainReceivesItsOwnMeasures(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode      = 'MTK' ;
        $product->eligibleUnitQuantityWeight    = 6.4 ;
        $product->eligibleUnitQuantityVolume    = 0.0128 ;

        $product->eligiblePackageQuantityCode   = 'PK' ;
        $product->eligiblePackageQuantityValue  = 0.456 ;
        $product->eligiblePackageQuantityWeight = 2.9184 ;

        $product->eligiblePalletQuantityCode    = 'PX' ;
        $product->eligiblePalletQuantityValue   = 38.304 ;
        $product->eligiblePalletQuantityWeight  = 245.1456 ;

        $unit    = $product->eligibleQuantity ;
        $package = $unit->valueReference ;
        $pallet  = $package->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $unit    ) ;
        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertInstanceOf( PhysicalQuantity::class , $pallet  ) ;

        $this->assertSame( 6.4      , $unit->weight    ) ;
        $this->assertSame( 0.0128   , $unit->volume    ) ;
        $this->assertSame( 2.9184   , $package->weight ) ;
        $this->assertSame( 245.1456 , $pallet->weight  ) ;

        // the ratio between two levels restates the packaging chain :
        // 245.1456 / 2.9184 = 84 boards to a pallet
        $this->assertEqualsWithDelta( 84 , $pallet->weight / $package->weight , 0.001 ) ;
    }

    /**
     * 🔑 **A level that states no weight keeps none.** A zero would read as
     * « weightless » where the truth is « unknown » — and nothing here derives
     * a missing measure from a volume and a density.
     *
     * @throws ReflectionException
     */
    public function testALevelWithoutMeasuresStaysWithoutThem(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode      = 'MTK' ;
        $product->eligibleUnitQuantityWeight    = 6.4 ;
        $product->eligiblePackageQuantityCode   = 'PK' ;
        $product->eligiblePackageQuantityValue  = 12 ;

        $package = $product->eligibleQuantity->valueReference ;

        $this->assertSame( 6.4 , $product->eligibleQuantity->weight ) ;

        $this->assertNull( $package->weight , 'an unstated weight is absent, never zero' ) ;
        $this->assertNull( $package->volume ) ;
    }

    /**
     * A measure that is not a number is not a measure — the same rule the
     * quantity already follows.
     *
     * @throws ReflectionException
     */
    public function testANonNumericMeasureReadsAsUnknown(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode   = 'MTK' ;
        $product->eligibleUnitQuantityWeight = 'n/a' ;

        $this->assertNull( $product->eligibleQuantity->weight ) ;
    }

    /**
     * ⚠️ **A level with no unit code assembles nothing**, weight or no weight :
     * a measure with no level to name it has nowhere to go.
     *
     * @throws ReflectionException
     */
    public function testAMeasureAloneBuildsNoChain(): void
    {
        $product = new Product() ;

        $this->assertFalse
        (
            $product->setEligibleQuantityProperty( Oihana::ELIGIBLE_UNIT_QUANTITY_WEIGHT , 6.4 )
        );

        $this->assertNull( $product->eligibleQuantity ) ;
    }

    /**
     * The measures reach the reading seam the rest of the code goes through :
     * the deeper levels arrive there as raw arrays, so the node has to be
     * rebuilt with them.
     *
     * @throws ReflectionException
     */
    public function testFindEligibleQuantityByTypeCarriesTheMeasuresOfTheLevel(): void
    {
        $product = new Product() ;

        $product->eligibleUnitQuantityCode      = 'MTK' ;
        $product->eligiblePackageQuantityCode   = 'PK' ;
        $product->eligiblePackageQuantityValue  = 0.456 ;
        $product->eligiblePackageQuantityWeight = 2.9184 ;
        $product->eligiblePackageQuantityVolume = 0.0058 ;

        $package = $product->findEligibleQuantityByType( UnitOfSaleType::PACKAGE ) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertSame( 2.9184 , $package->weight ) ;
        $this->assertSame( 0.0058 , $package->volume ) ;
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

        // the level found types the chain below it : `->weight` at any depth
        $this->assertInstanceOf( PhysicalQuantity::class , $unit->valueReference ) ;

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
    // ---- hasApplicableResource

    /**
     * The links are typed by the attribute on the property.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testHasApplicableResourceIsHydrated(): void
    {
        $product = new Reflection()->hydrate( $this->productWithApplicableResources() , Product::class ) ;

        $links = $product->hasApplicableResource ;

        $this->assertIsArray( $links );
        $this->assertCount( 2 , $links );

        $this->assertInstanceOf( ApplicableResource::class , $links[ 0 ] );
        $this->assertInstanceOf( ApplicableResource::class , $links[ 1 ] );

        $this->assertTrue ( $links[ 0 ]->appliedByDefault );
        $this->assertFalse( $links[ 1 ]->appliedByDefault );

        $this->assertSame( 1 , $links[ 0 ]->position );
        $this->assertSame( 2 , $links[ 1 ]->position );
    }

    /**
     * 🔑 **The essay that keeps the helper honest.** A payload reaches this class
     * by two doors — the constructor and `Reflection::hydrate()` — and the
     * attribute only covers the second. Comparing them is what catches a
     * property typed on one side and left raw on the other.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testBothHydrationPathsAgreeOnApplicableResources(): void
    {
        $payload = $this->productWithApplicableResources() ;

        $reflected  = new Reflection()->hydrate( $payload , Product::class ) ;
        $constructed = new Product( $payload ) ;

        $fromReflection  = $reflected->hasApplicableResource ;
        $fromConstructor = array_map( hydrateApplicableResource( ... ) , (array) $constructed->hasApplicableResource ) ;

        // ⚠️ What the two must agree on is what they SAY, not what they hold :
        // `Reflection::hydrate()` leaves an internal reflection handle on the
        // objects it builds, and the constructor does not. Comparing the
        // serialized form asks the only question that matters.
        $this->assertEquals
        (
            json_decode( json_encode( $fromReflection  ) , true ) ,
            json_decode( json_encode( $fromConstructor ) , true ) ,
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function productWithApplicableResources() :array
    {
        return
        [
            Oihana::ID                     => 'board-42' ,
            Oihana::HAS_APPLICABLE_RESOURCE =>
            [
                [
                    Oihana::ITEM               => [ Oihana::ID => 'srv-treatment' ] ,
                    Oihana::POSITION           => 1 ,
                    Oihana::APPLIED_BY_DEFAULT => true ,
                ],
                [
                    Oihana::ITEM               => [ Oihana::ID => 'srv-polish' ] ,
                    Oihana::POSITION           => 2 ,
                    Oihana::APPLIED_BY_DEFAULT => false ,
                ],
            ]
        ];
    }
}