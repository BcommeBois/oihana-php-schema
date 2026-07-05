<?php

namespace xyz\oihana\schema\products;

use ReflectionException;

use oihana\core\strings\SanitizeFlag;
use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\DefinedTerm;
use org\schema\SomeProducts ;
use org\schema\QuantitativeValue;

use org\schema\constants\Schema;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use org\unece\uncefact\MeasureCode;
use org\unece\uncefact\PackageCode;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\ProductAdditionalProperty;
use xyz\oihana\schema\enumerations\UnitOfSaleType ;

use function oihana\core\objects\toAssociativeArray;
use function oihana\core\strings\sanitize;

/**
 * A generic product representation enriched with commerce metadata :
 * unit of sale, eligible quantities (unit → package → pallet), pricing
 * categories, tax rate and stock information.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class Product extends SomeProducts
{
    use SetAdditionalPropertyTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The density of the product.
     * @var float|null
     */
    public ?float $density ;

    /**
     * The eligible quantity of the product, representing the minimum or specific ordering quantities
     * at different levels: unit, package, and pallet.
     *
     * The structure is a `QuantitativeValue` object where:
     *  - `value` is the quantity
     *  - `unitCode` is the UN/CEFACT code of the unit
     *  - `unitText` is the human-readable name of the unit
     *  - `valueReference` (optional) can point to a nested `QuantitativeValue`
     *     for higher aggregation levels: unit → package → pallet
     *
     * Example structure:
     * ```json
     * {
     *     "value": "1",  // unit quantity
     *     "unitCode": "EA",
     *     "unitText": "each",
     *     "valueReference": // package level
     *     {
     *         "value": "12",
     *         "unitCode": "BOX",
     *         "unitText": "box",
     *         "valueReference": { // pallet level
     *             "value": "48",
     *             "unitCode": "PLT",
     *             "unitText": "pallet"
     *         }
     *     }
     * }
     * ```
     * @var QuantitativeValue|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|QuantitativeValue $eligibleQuantity = null ;

    /**
     * Indicates if the product is managed in stock.
     * @var null|bool
     */
    public null|bool $inStock ;

    /**
     * The current approximate inventory level for the item or items.
     * @var array|StockLevel|null
     */
    #[HydrateWith(StockLevel::class)]
    public null|array|QuantitativeValue $inventoryLevel = null ;

    /**
     * The  length of the product.
     * @var float|null
     */
    public ?float $length ;

    /**
     * The price category of the product.
     * @var array|string|object|null
     */
    public array|string|null|object $priceCategory = null ;

    /**
     * Product functional type (stock, tracking, rules, etc).
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $productType = null ;

    /**
     * The status of the product.
     * @var int|null
     */
    public ?int $status ;

    /**
     * A unit of sale refers to the specific quantity or grouping of a product or service that you offer for purchase.
     *  - UNIT    : single item
     *  - PACKAGE : grouped items (box, carton, etc.)
     *  - PARCEL  : larger aggregation (pallet, parcel of packages)
     * @var string|null
     * @see UnitOfSaleType Enumeration of allowed values for the {@see $unitOfSale} property.
     */
    public ?string $unitOfSale ;

    /**
     * The taxe rate information of the product.
     * @var array|TaxRate|string|int|null
     */
    public array|TaxRate|string|int|null $vat ;

    /**
     * The volume of the product.
     * @var null|float|int
     */
    public null|float|int $volume ;

    /**
     * The product's web category (display, navigation, etc.)
     * @var array|string|DefinedTerm|null
     */
    public array|string|DefinedTerm|null $webCategory ;

    /**
     * Find a QuantitativeValue inside eligibleQuantity tree matching the given UnitOfSaleType.
     *
     * This method recursively checks eligibleQuantity and its successive
     * valueReference objects until a QuantitativeValue with a matching
     * additionalType (UNIT, PACKAGE, PARCEL) is found.
     *
     * Example:
     * ```php
     * $this->findEligibleQuantityByType(UnitOfSaleType::PACKAGE);
     * ```
     *
     * @param string $type One of the UnitOfSaleType::* constants.
     *
     * @return QuantitativeValue|null
     * @throws ReflectionException
     */
    public function findEligibleQuantityByType( string $type ) :?QuantitativeValue
    {
        return $this->searchEligibleQuantityByType( $type , $this->eligibleQuantity ?? null ) ;
    }

    /**
     * Calculates the inventory level in the unit of sale.
     *
     * @param StockLevel|null $inventoryLevel The inventory level (in base unit).
     *
     * @return float|null The inventory level converted to the unit of sale, or null if calculation is not possible.
     *
     * @throws ReflectionException
     */
    public function getInventoryLevelInUnitOfSale( ?StockLevel $inventoryLevel ): ?float
    {
        if ( !$inventoryLevel || !isset( $inventoryLevel->value ) )
        {
            return null;
        }

        $factor = $this->getUnitOfSaleConversionFactor();

        return $factor > 0 ? $inventoryLevel->value / $factor : null;
    }

    /**
     * Get the conversion factor from base unit to the unit of sale.
     *
     * @return float The multiplication factor, or 1.0 if unitOfSale is UNIT.
     *
     * @throws ReflectionException
     */
    public function getUnitOfSaleConversionFactor(): float
    {
        $unitOfSale = $this->unitOfSale ?? null ;

        if ( !$unitOfSale || $unitOfSale === UnitOfSaleType::UNIT )
        {
            return 1.0;
        }

        $factor = 1.0 ;
        $currentQV = $this->eligibleQuantity ?? null ;

        while ( $currentQV )
        {
            if ( is_array( $currentQV ) )
            {
                $currentQV = new QuantitativeValue( $currentQV );
            }

            $additionalType = $currentQV->additionalType ?? null;

            if ( isset( $currentQV->value ) && $currentQV->value != 0 )
            {
                if ( $additionalType !== UnitOfSaleType::UNIT )
                {
                    $factor *= (float) $currentQV->value ;
                }
            }

            if ( $additionalType === $unitOfSale )
            {
                break ;
            }

            $currentQV = $currentQV->valueReference ?? null ;
        }

        return $factor ;
    }

    /**
     * Magic setter for the Product class.
     *
     * This method allows setting dynamic properties of the product.
     * It delegates the handling to specific setter methods and stops immediately
     * once a property has been successfully handled.
     *
     * The order of handling is important:
     *  1. setAdditionalProperties() – sets optional additional properties.
     *  2. setEligibleQuantityProperty() – sets the eligibleQuantity hierarchy (unit → package → pallet).
     *
     * If a property is not handled by any of these methods, it is silently ignored.
     *
     * @param string $property The property name being set.
     * @param mixed  $value    The value to assign to the property.
     *
     * @return void
     *
     * @throws ReflectionException if an error occurs while processing eligibleQuantity.
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setAdditionalProperties     ( $property , $value ) ||
        $this->setEligibleQuantityProperty ( $property , $value ) ;
    }

    /**
     * Set the optional additional properties with the magic _set method.
     *
     * @param string $property Property name
     * @param mixed  $value    Value of the property.
     *
     * @return bool True if the property was handled, false otherwise
     *
     * @throws ReflectionException
     */
    public function setAdditionalProperties( string $property , mixed $value ) :bool
    {
        if( !ProductAdditionalProperty::includes( $property ) )
        {
            return false ;
        }

        $value = match( $property )
        {
            ProductAdditionalProperty::GRAIN => (bool) $value ,
            default                          => $value
        };

        if( is_string( $value ) )
        {
            $value = sanitize( $value , SanitizeFlag::DEFAULT | SanitizeFlag::NULLIFY ) ;
        }

        if( !isset( $value ) )
        {
            return false ;
        }

        $this->setAdditionalProperty
        ([
            Schema::PROPERTY_ID => $property ,
            Schema::VALUE       => $value
        ]) ;

        return true;
    }

     /**
      * Set the optional eligibleQuantity property with the magic _set method.
      *
      * @param string $property Property name
      * @param mixed  $value    Value of the property.
      *
      * @return bool True if the property was handled, false otherwise
      *
      * @throws ReflectionException
      */
    public function setEligibleQuantityProperty( string $property , mixed $value ) :bool
    {
        $mapping =
        [
            Oihana::ELIGIBLE_UNIT_QUANTITY_CODE     => [ 0 , 0 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_CODE  => [ 1 , 0 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_VALUE => [ 1 , 1 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_CODE   => [ 2 , 0 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_VALUE  => [ 2 , 1 ] ,
        ];

        if ( !isset( $mapping[ $property ] ) )
        {
            return false ;
        }

        [ $level , $type ] = $mapping[ $property ] ;

        $this->_eligibleQuantityDefinition ??=
        [
            [ null , 1    , UnitOfSaleType::UNIT    ] , // unit
            [ null , null , UnitOfSaleType::PACKAGE ] , // package
            [ null , null , UnitOfSaleType::PARCEL  ] , // pallet
        ];

        if ( $type === 0 && !empty( $value ) )
        {
            $this->_eligibleQuantityDefinition[ $level ][0] = $this->resolveUnitCode( $value ) ;
        }
        else if ($type === 1 )
        {
            $this->_eligibleQuantityDefinition[ $level ][1] = $value ;
        }

        $this->eligibleQuantity = null ;

        [ $unitCode    , $unitValue    , $unitType    ] = $this->_eligibleQuantityDefinition[0] ;
        [ $packageCode , $packageValue , $packageType ] = $this->_eligibleQuantityDefinition[1] ;
        [ $palletCode  , $palletValue  , $palletType  ] = $this->_eligibleQuantityDefinition[2] ;

        if ( empty( $unitCode ) && empty( $packageCode ) && empty( $palletCode ) )
        {
            return false ;
        }

        $createQV = function( ?string $code, mixed $val , string $type ) :?QuantitativeValue
        {
            if ( empty( $code ) && empty( $val ) )
            {
                return null ;
            }

            return new QuantitativeValue
            ([
                Schema::ADDITIONAL_TYPE => $type ,
                Schema::VALUE           => $val !== null ? (float) $val : null,
                Schema::UNIT_CODE       => $code,
                Schema::UNIT_TEXT       => $code ? ( MeasureCode::getName( $code ) ?? PackageCode::getName( $code ) ) : null
            ]);
        };

        $unitQV    = $createQV( $unitCode    , $unitValue    , $unitType    ) ;
        $packageQV = $createQV( $packageCode , $packageValue , $packageType ) ;
        $palletQV  = $createQV( $palletCode  , $palletValue  , $palletType  ) ;

        if ( $packageQV && $palletQV )
        {
            $packageQV->valueReference = $palletQV ;
        }

        if ( $unitQV && $packageQV )
        {
            $unitQV->valueReference = $packageQV ;
        }

        $this->eligibleQuantity = $unitQV ;

        return true ;
    }

    /**
     * Resolves a raw unit code expression into a normalized UN/CEFACT unit code.
     *
     * By default the value is returned unchanged. Override this method in a subclass
     * to map a proprietary nomenclature (e.g. an ERP specific unit code) to its
     * UN/CEFACT equivalent before the {@see $eligibleQuantity} tree is built.
     *
     * @param mixed $value The raw unit code expression to resolve.
     *
     * @return string|null The normalized UN/CEFACT unit code, or null if the value is empty.
     */
    protected function resolveUnitCode( mixed $value ) :?string
    {
        return isset( $value ) ? (string) $value : null ;
    }

    /**
     * @var array|null
     */
    private ?array $_eligibleQuantityDefinition = null;

    /**
     * Recursive internal search for a QuantitativeValue by UnitOfSaleType.
     * @param string $type One of the UnitOfSaleType::* constants.
     * @param array|QuantitativeValue|null $qv
     * @return QuantitativeValue|null
     * @throws ReflectionException
     */
    private function searchEligibleQuantityByType( string $type , array|QuantitativeValue|null $qv ): ?QuantitativeValue
    {
        if ( !$qv )
        {
            return null ;
        }

        if( $qv instanceof QuantitativeValue )
        {
            $qv = toAssociativeArray( $qv ) ; // enforce the object to be an associative array to fix the cache problem
        }

        $additionalType = $qv[ Schema::ADDITIONAL_TYPE ] ?? null ;

        if ( $additionalType === $type )
        {
            return $qv instanceof QuantitativeValue ? $qv : new QuantitativeValue( $qv ) ;
        }

        $qv = $this->searchEligibleQuantityByType( $type , $qv[ Schema::VALUE_REFERENCE ] ?? null ) ;

        return $qv == null ? null : new QuantitativeValue( $qv ) ;
    }
}