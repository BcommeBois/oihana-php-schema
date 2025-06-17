<?php

namespace org\schema\constants\uncefact;

use org\schema\constants\properties\Prop;
use org\schema\PropertyValue;
use oihana\reflections\traits\ConstantsTrait;

/**
 * UN/CEFACT Unit of Measure Symbols and Codes Enumeration Class (Recommendation 20).
 *
 * This class provides an extended selection of the most commonly used codes
 * across various commercial and logistical contexts.
 *
 * For an exhaustive list and official documentation, please consult:
 * @see https://unece.org/trade/uncefact/cl-recommendations
 * @example
 * ```
 * <?php
 * $logger->info( json_encode( MeasureSymbols::PERCENT ) );
 * $logger->info( json_encode( MeasureSymbols::getCode( UnitSymbols::PERCENT ) ) );
 * $logger->info( json_encode( MeasureSymbols::getName( UnitSymbols::PERCENT ) ) );
 * $logger->info( json_encode( MeasureSymbols::get( UnitSymbols::PERCENT ) ) );
 * ```
 */
class MeasureSymbol
{
    use ConstantsTrait ;

    // =====================================================================
    // Quantity Units
    // =====================================================================

    public const string PIECE     = 'pc'   ;  // Piece / Each
    public const string UNIT      = 'un'   ;  // Each / Unit (Using 'un' for unit symbol)
    public const string PAIR      = 'pr'   ;  // Pair
    public const string DOZEN     = 'dz'   ;  // Dozen
    public const string GROSS     = 'gr'   ;  // Gross (144 units)
    public const string HUNDRED   = '100'  ;
    public const string THOUSAND  = '1000' ;
    public const string TEN_PAIRS = '10pr' ;

    // =====================================================================
    // Mass Units (Weight)
    // =====================================================================

    public const string KILOGRAM   = 'kg' ;
    public const string GRAM       = 'g'  ;
    public const string MILLIGRAM  = 'mg' ;
    public const string METRIC_TON = 't'  ;
    public const string POUND      = 'lb' ;
    public const string OUNCE      = 'oz' ;
    public const string CARAT      = 'ct' ;

    // =====================================================================
    // Length Units
    // =====================================================================

    public const string METER      = 'm'  ;
    public const string CENTIMETER = 'cm' ;
    public const string HECTOMETER = 'hm' ; // 100 meters
    public const string MILLIMETER = 'mm' ;
    public const string KILOMETER  = 'km' ;
    public const string INCH       = 'in' ;
    public const string FOOT       = 'ft' ;
    public const string YARD       = 'yd' ;
    public const string MILE       = 'mi' ; // Statute mile

    // =====================================================================
    // Area Units
    // =====================================================================

    public const string ACRE              = 'ac';  // Acre
    public const string ACRE_FOOT         = 'ac-ft'; // Acre Foot (volume unit symbol)
    public const string HECTARE           = 'ha';  // Hectare
    public const string SQUARE_CENTIMETER = 'cm²'; // Square Centimeter
    public const string SQUARE_DECIMETER  = 'dm²'; // Square Decimeter
    public const string SQUARE_FOOT       = 'ft²'; // Square Foot
    public const string SQUARE_INCH       = 'in²'; // Square Inch
    public const string SQUARE_KILOMETER  = 'km²'; // Square Kilometer
    public const string SQUARE_METER      = 'm²';  // Square Meter (M2)
    public const string SQUARE_MILLIMETER = 'mm²'; // Square Millimeter
    public const string SQUARE_MILE       = 'mi²'; // Square Mile (statute mile)
    public const string SQUARE_YARD       = 'yd²'; // Square Yard

    // =====================================================================
    // Volume Units
    // =====================================================================

    public const string LITER            = 'L';   // Liter
    public const string MILLILITER       = 'mL';  // Milliliter
    public const string CUBIC_METER      = 'm³';  // Cubic Meter (M3)
    public const string CUBIC_CENTIMETER = 'cm³'; // Cubic Centimeter
    public const string CUBIC_DECIMETER  = 'dm³'; // Cubic Decimeter
    public const string US_GALLON        = 'gal (US)'; // US Gallon
    public const string IMPERIAL_GALLON  = 'gal (Imp)'; // Imperial Gallon (UK)
    public const string BARREL           = 'bbl'; // Barrel
    public const string CUBIC_FOOT       = 'ft³'; // Cubic Foot

    // =====================================================================
    // Time Units
    // =====================================================================

    public const string DAY    = 'day'; // Day
    public const string HOUR   = 'h';   // Hour
    public const string MINUTE = 'min'; // Minute
    public const string SECOND = 's';   // Second
    public const string MONTH  = 'month';// Month
    public const string YEAR   = 'yr';  // Year
    public const string WEEK   = 'wk';  // Week

    // =====================================================================
    // Percentage and Ratio Units
    // =====================================================================

    public const string PERCENT           = '%'   ;
    public const string PER_THOUSAND      = '‰'   ;
    public const string PARTS_PER_MILLION = 'ppm' ;

    // =====================================================================
    // Energy Units
    // =====================================================================

    public const string KILOWATT_HOUR = 'kWh'  ;
    public const string JOULE         = 'J'    ;
    public const string KILOJOULE     = 'kJ'   ;
    public const string CALORIE       = 'cal'  ; // (large calorie)
    public const string KILOCALORIE   = 'kcal' ;

    // =====================================================================
    // Pressure Units
    // =====================================================================

    public const string PASCAL                = 'Pa'   ;
    public const string BAR                   = 'bar'  ;
    public const string MILLIBAR              = 'mbar' ;
    public const string POUND_PER_SQUARE_INCH = 'psi'  ;

    // =====================================================================
    // Temperature Units
    // =====================================================================

    public const string CELSIUS    = '°C' ;
    public const string FAHRENHEIT = '°F' ;
    public const string KELVIN     = 'K'  ;

    // =====================================================================
    // Generic / Dimensionless Units & Factors
    // =====================================================================

    public const string COUNT           = '#'     ; // Similar to NMB, often for specific items
    public const string NUMBER          = 'qty'   ; // Number / Quantity (using 'qty' for symbol)
    public const string RATIO           = 'ratio' ;
    public const string UNIT_OF_CAPITAL = 'UC'    ; // Unit of Capital - keeping as is, but reminder `priceCurrency` is preferred.
    public const string SCORE           = 'score' ; // For points or a numerical rating
    public const string POINT           = 'pt'    ; // e.g., a single point in a system

    // =====================================================================
    // Degrees Units
    // =====================================================================

    public const string ANGULAR = '°';   // Angular Degree
    public const string RADIAN  = 'rad'; // Radian

    // =====================================================================
    // Common Miscellaneous Units
    // =====================================================================

    public const string AMPERE                = 'A'   ;
    public const string BECQUEREL             = 'Bq'  ;
    public const string BIT                   = 'bit' ;
    public const string BYTE                  = 'B'   ;
    public const string COULOMB               = 'C'   ;
    public const string DECIBEL               = 'dB'  ;
    public const string FARAD                 = 'F'   ;
    public const string GIGABYTE              = 'GB'  ; // No direct UN/CEFACT code, symbol is standard
    public const string GRAY                  = 'Gy'  ;
    public const string HENRY                 = 'H'   ;
    public const string HERTZ                 = 'Hz'  ;
    public const string KILOHERTZ             = 'kHz' ;
    public const string KILOBYTE              = 'KB'  ;
    public const string KILOWATT              = 'kW'  ;
    public const string LUMEN                 = 'lm'  ;
    public const string LUX                   = 'lx'  ;
    public const string MEGAHERTZ             = 'MHz' ;
    public const string MEGABYTE              = 'MB'  ;
    public const string NEWTON                = 'N'   ;
    public const string OHM                   = 'Ω'   ;
    public const string POUND_FORCE           = 'lbf' ;
    public const string REVOLUTION_PER_MINUTE = 'rpm' ;
    public const string SIEVERT               = 'Sv'  ;
    public const string SIEMENS               = 'S'   ;
    public const string TESLA                 = 'T'   ;
    public const string VOLT                  = 'V'   ;
    public const string WATT                  = 'W'   ;
    public const string WEBER                 = 'Wb'  ;

    // =====================================================================
    // Private
    // =====================================================================

    private static ?array $CODES = null ;
    private static ?array $NAMES = null ;

    // =====================================================================
    // Methods
    // =====================================================================

    /**
     * Returns the UnitCode definition (name, unitCode and unitText) of the specific symbol
     * or null if the symbol is not valid.
     * @param string $symbol
     * @return ?PropertyValue
     */
    public static function get( string $symbol ): ?PropertyValue
    {
        if( self::isValid( $symbol ) )
        {
            return new PropertyValue
            ([
                Prop::NAME       => self::getName( $symbol ) ,
                Prop::UNIT_CODE  => self::getCode( $symbol ) ,
                Prop::UNIT_TEXT  => $symbol ,
            ] );
        }
        return null ;
    }

    /**
     * Returns the official UN/CEFACT code for a given symbol.
     * @param string $symbol
     * @return string|null The UN/CEFACT code (e.g., 'P1') or null if not found.
     */
    public static function getCode( string $symbol ): ?string
    {
        if( static::$CODES === null )
        {
            static::$CODES = MeasureCode::getAll() ;
        }
        return static::$CODES[ self::getConstant( $symbol ) ] ?? null;
    }

    /**
     * Returns the symbol with a specific unit code.
     * @param string $code
     * @return string|null
     */
    public static function getFromCode( string $code ): ?string
    {
        return MeasureCode::getSymbol( $code ) ;
    }

    /**
     * Returns the symbol from a specific unit name.
     * @param string $name
     * @return string|null
     */
    public static function getFromName( string $name ): ?string
    {
        return MeasureName::getSymbol( $name ) ;
    }
    /**
     * Returns the official UN/CEFACT name for a given symbol.
     * @param string $code
     * @return string|null The UN/CEFACT name or null if not found.
     */
    public static function getName( string $code ): ?string
    {
        if( static::$NAMES === null )
        {
            static::$NAMES = MeasureName::getAll() ;
        }
        return static::$NAMES[ self::getConstant( $code ) ] ?? null;
    }

}