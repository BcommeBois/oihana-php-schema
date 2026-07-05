<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\QuantitativeValue;
use org\schema\SomeProducts;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\ProductAdditionalProperty;
use xyz\oihana\schema\enumerations\UnitOfSaleType;
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

}
