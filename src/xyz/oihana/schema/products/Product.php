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

use function oihana\core\strings\sanitize;

use function xyz\oihana\schema\helpers\hydrate\findPhysicalQuantityByType;

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
     *
     * Each node is hydrated as a {@see PhysicalQuantity}, the specialization
     * that lets a level carry its own weight and volume. It remains a
     * `QuantitativeValue`, so the declared type is left as it is : narrowing
     * it would reject the plain `QuantitativeValue` a consumer may still
     * assign.
     *
     * @var null|array|QuantitativeValue
     */
    #[HydrateAs(PhysicalQuantity::class)]
    public null|array|QuantitativeValue $eligibleQuantity = null ;

    /**
     * The fees this product owes on top of its price — an environmental
     * contribution, a deposit, packaging, carriage.
     *
     * A list rather than one property per kind : {@see PriceComponentType}
     * already enumerates several, they all behave the same way — a rate, a
     * unit, sometimes an issuing body — and one product may owe more than one.
     *
     * 🔑 **Each `price` is expressed in the unit the product is billed in**, so
     * applying a fee is `quantity × price` and nothing else. An entry without a
     * price says the fee is due but could not be quantified, and
     * {@see FeeSpecification::$unresolvedReason} says why.
     *
     * @var null|array|FeeSpecification
     * @since 1.4.0
     */
    #[HydrateWith(FeeSpecification::class)]
    public null|array|FeeSpecification $fees ;

    /**
     * The resources that may be applied to this product — a service it can
     * receive, an option it can take — each with its rank and whether it
     * applies by default.
     *
     * 🚨 **Every entry is a LINK, not a resource** : the flag saying « applies
     * by default » belongs to the pair (this product, that resource) and to
     * nothing else. The same service can be included on one product and merely
     * offered on the next — {@see ApplicableResource} carries that, a bare list
     * of products could not.
     *
     * 🔑 **The entries hold references, not records.** The price and the unit
     * of a resource live on its own record ; a price also depends on who is
     * buying, so freezing one here would answer the wrong question.
     *
     * @var array|ApplicableResource|null
     */
    #[HydrateWith(ApplicableResource::class)]
    public null|array|ApplicableResource $hasApplicableResource ;

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
     * The walk itself is {@see \xyz\oihana\schema\helpers\hydrate\findPhysicalQuantityByType()},
     * which takes the chain as a parameter : this product's own is only one of
     * the chains worth walking, and it is `null` on any product read back from
     * a base — the tree is copied onto the offers, and it is that copy which is
     * stored.
     *
     * @param string $type One of the UnitOfSaleType::* constants.
     *
     * @return QuantitativeValue|null
     * @throws ReflectionException
     */
    public function findEligibleQuantityByType( string $type ) :?QuantitativeValue
    {
        return findPhysicalQuantityByType( $type , $this->eligibleQuantity ?? null ) ;
    }

    /**
     * Calculates the inventory level in the unit of sale.
     *
     * A non-numeric `value` — the raw shape a base read can leave on the stock
     * level — yields `null` like an absent one, rather than a `TypeError` on the
     * division.
     *
     * @param StockLevel|null $inventoryLevel The inventory level (in base unit).
     *
     * @return float|null The inventory level converted to the unit of sale, or null if calculation is not possible.
     *
     * @throws ReflectionException
     */
    public function getInventoryLevelInUnitOfSale( ?StockLevel $inventoryLevel ): ?float
    {
        $value = $inventoryLevel->value ?? null ;

        if ( !is_numeric( $value ) )
        {
            return null;
        }

        $factor = $this->getUnitOfSaleConversionFactor();

        return $factor > 0 ? (float) $value / $factor : null ;
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
      * Builds the *unit → package → pallet* chain from the flat keys a source
      * hands over one at a time — the codes and quantities of each level, plus
      * what that level weighs and what it occupies. Every node is a
      * {@see PhysicalQuantity}, so a measure has somewhere to sit.
      *
      * 🔑 **Nothing is computed here.** A level that states no weight keeps
      * none : deriving one from a volume and a density would produce a figure
      * indistinguishable from a stated one, and this class has no way to say
      * which is which.
      *
      * ⚠️ **A level with no unit code assembles nothing**, weight or no weight :
      * a measure with no level to name it has nowhere to go.
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
        // Each entry names the level the key feeds — unit, package, pallet — and
        // the slot it fills in that level's buffer, described below.
        $mapping =
        [
            Oihana::ELIGIBLE_UNIT_QUANTITY_CODE      => [ 0 , 0 ] ,
            Oihana::ELIGIBLE_UNIT_QUANTITY_WEIGHT    => [ 0 , 3 ] ,
            Oihana::ELIGIBLE_UNIT_QUANTITY_VOLUME    => [ 0 , 4 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_CODE   => [ 1 , 0 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_VALUE  => [ 1 , 1 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_WEIGHT => [ 1 , 3 ] ,
            Oihana::ELIGIBLE_PACKAGE_QUANTITY_VOLUME => [ 1 , 4 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_CODE    => [ 2 , 0 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_VALUE   => [ 2 , 1 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_WEIGHT  => [ 2 , 3 ] ,
            Oihana::ELIGIBLE_PALLET_QUANTITY_VOLUME  => [ 2 , 4 ] ,
        ];

        if ( !isset( $mapping[ $property ] ) )
        {
            return false ;
        }

        [ $level , $slot ] = $mapping[ $property ] ;

        $this->_eligibleQuantityDefinition ??=
        [
            //  code , value , additionalType          , weight , volume
            [ null , 1    , UnitOfSaleType::UNIT    , null , null ] , // unit
            [ null , null , UnitOfSaleType::PACKAGE , null , null ] , // package
            [ null , null , UnitOfSaleType::PARCEL  , null , null ] , // pallet
        ];

        if ( $slot === 0 )
        {
            // The keys arrive one at a time and the source leaves the unused
            // levels blank : an empty code must never erase one already resolved.
            if ( !empty( $value ) )
            {
                $this->_eligibleQuantityDefinition[ $level ][ 0 ] = $this->resolveUnitCode( $value ) ;
            }
        }
        else
        {
            $this->_eligibleQuantityDefinition[ $level ][ $slot ] = $value ;
        }

        $this->eligibleQuantity = null ;

        [ $unitCode    , $unitValue    , $unitType    , $unitWeight    , $unitVolume    ] = $this->_eligibleQuantityDefinition[0] ;
        [ $packageCode , $packageValue , $packageType , $packageWeight , $packageVolume ] = $this->_eligibleQuantityDefinition[1] ;
        [ $palletCode  , $palletValue  , $palletType  , $palletWeight  , $palletVolume  ] = $this->_eligibleQuantityDefinition[2] ;

        if ( empty( $unitCode ) && empty( $packageCode ) && empty( $palletCode ) )
        {
            return false ;
        }

        $createQV = function( ?string $code , mixed $val , string $type , mixed $weight , mixed $volume ) :?PhysicalQuantity
        {
            if ( empty( $code ) && empty( $val ) )
            {
                return null ;
            }

            return new PhysicalQuantity
            ([
                Schema::ADDITIONAL_TYPE => $type ,
                // Only a numeric value converts : anything else would cast to a
                // meaningless 0.0 (or raise on an object) instead of saying "unknown".
                Schema::VALUE           => is_numeric( $val ) ? (float) $val : null,
                Schema::UNIT_CODE       => $code,
                Schema::UNIT_TEXT       => $code ? ( MeasureCode::getName( $code ) ?? PackageCode::getName( $code ) ) : null,
                // Same rule for the measures, and for the same reason : a level
                // that states no weight stays without one. A zero would read as
                // « weightless » where the truth is « unknown ».
                Oihana::WEIGHT          => is_numeric( $weight ) ? (float) $weight : null,
                Oihana::VOLUME          => is_numeric( $volume ) ? (float) $volume : null,
            ]);
        };

        $unitQV    = $createQV( $unitCode    , $unitValue    , $unitType    , $unitWeight    , $unitVolume    ) ;
        $packageQV = $createQV( $packageCode , $packageValue , $packageType , $packageWeight , $packageVolume ) ;
        $palletQV  = $createQV( $palletCode  , $palletValue  , $palletType  , $palletWeight  , $palletVolume  ) ;

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
     * Only a scalar can name a unit : an array or an object yields `null` rather
     * than being cast, which produced the literal string `"Array"` for the first
     * and a fatal `Error` for the second.
     *
     * @param mixed $value The raw unit code expression to resolve.
     *
     * @return string|null The normalized UN/CEFACT unit code, or null if the value
     *                     is empty or not a scalar.
     */
    protected function resolveUnitCode( mixed $value ) :?string
    {
        return is_scalar( $value ) ? (string) $value : null ;
    }

    /**
     * The buffer the flat keys fill in, one at a time, before the chain is
     * rebuilt : three levels — unit, package, pallet — of five slots each.
     *
     *     [ 0 ] unitCode  [ 1 ] value  [ 2 ] additionalType  [ 3 ] weight  [ 4 ] volume
     *
     * Only slot 2 is never written from outside : the level a slot belongs to
     * is what names it.
     *
     * @var array|null
     */
    private ?array $_eligibleQuantityDefinition = null;
}