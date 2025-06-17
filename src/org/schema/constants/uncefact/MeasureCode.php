<?php

namespace org\schema\constants\uncefact;

use oihana\reflections\traits\ConstantsTrait;
use org\schema\constants\properties\Prop;
use org\schema\PropertyValue;

/**
 * UN/CEFACT Unit of Measure Codes Enumeration Class (Recommendation 20).
 *
 * This class provides an extended selection of the most commonly used codes
 * across various commercial and logistical contexts.
 *
 * For an exhaustive list and official documentation, please consult:
 * @see https://unece.org/trade/uncefact/cl-recommendations
 * @example
 * ```
 * <?php
 * $logger->info( MeasureCode::PERCENT ) ; // 'PC1'
 * $logger->info( MeasureCode::getName( UnitCodes::PERCENT ) ) ; // 'Percent'
 * $logger->info( MeasureCode::getSymbol( UnitCodes::PERCENT ) ) ; // '%'
 * $logger->info( json_encode( MeasureCode::get( UnitCodes::PERCENT ) ) ); // {"name":"Percent","unitCode":"PC1","unitText":"%"}
 * $logger->info( MeasureCode::getFromName( UnitNames::PERCENT ) ) ; // 'PC1'
 * $logger->info( MeasureCode::getFromSymbol( UnitSymbols::PERCENT ) ) ; // 'PC1'
 * ```
 */
class MeasureCode
{
    use ConstantsTrait ;

    // =====================================================================
    // Quantity Units (Unités de quantité)
    // =====================================================================

    public const string PIECE     = 'PCE' ; // Piece / Each
    public const string UNIT      = 'C62' ; // Each / Unit (often synonymous with PCE)
    public const string PAIR      = 'PR'  ; // Pair
    public const string DOZEN     = 'DZN' ; // Dozen
    public const string GROSS     = 'GRO' ; // Gross (144 units)
    public const string HUNDRED   = 'CEN' ; // Hundred
    public const string THOUSAND  = 'THM' ; // Thousand
    public const string TEN_PAIRS = 'DPA' ; // Ten pairs

    // =====================================================================
    // Mass Units (Weight)
    // =====================================================================

    public const string CARAT      = 'CT'  ; // Carat
    public const string GRAM       = 'GRM' ; // Gram
    public const string KILOGRAM   = 'KGM' ; // Kilogram
    public const string METRIC_TON = 'TNE' ; // Metric Ton
    public const string MILLIGRAM  = 'MGM' ; // Milligram
    public const string OUNCE      = 'OZA' ; // Ounce
    public const string POUND      = 'LBR' ; // Pound

    // =====================================================================
    // Length Units
    // =====================================================================

    public const string CENTIMETER = 'CMT' ; // Centimeter
    public const string FOOT       = 'FOT' ; // Foot
    public const string HECTOMETER = 'HMT' ; // Hectometer (100 meters)
    public const string INCH       = 'INH' ; // Inch
    public const string KILOMETER  = 'KMT' ; // Kilometer
    public const string METER      = 'MTR' ; // Meter
    public const string MILE       = 'SMI' ; // Mile (statute mile)
    public const string MILLIMETER = 'MMT' ; // Millimeter
    public const string YARD       = 'YRD' ; // Yard

    // =====================================================================
    // Area Units
    // =====================================================================

    public const string ACRE              = 'ACR' ; // Acre
    public const string ACRE_FOOT         = 'AFK' ; // Acre Foot
    public const string HECTARE           = 'HAR' ; // Hectare
    public const string SQUARE_CENTIMETER = 'CMK' ; // Square Centimeter
    public const string SQUARE_DECIMETER  = 'DMK' ; // Square Decimeter
    public const string SQUARE_FOOT       = 'FTK' ; // Square Foot
    public const string SQUARE_INCH       = 'INK' ; // Square Inch
    public const string SQUARE_KILOMETER  = 'KMK' ; // Square Kilometer
    public const string SQUARE_METER      = 'MTK' ; // Square Meter (M2)
    public const string SQUARE_MILLIMETER = 'MMK' ; // Square Millimeter
    public const string SQUARE_MILE       = 'SMK' ; // Square Mile (statute mile)
    public const string SQUARE_YARD       = 'YKM' ; // Square Yard

    // =====================================================================
    // Volume Units
    // =====================================================================

    public const string LITER            = 'LTR' ; // Liter
    public const string MILLILITER       = 'MLT' ; // Milliliter
    public const string CUBIC_METER      = 'MTQ' ; // Cubic Meter (M3)
    public const string CUBIC_CENTIMETER = 'CMQ' ; // Cubic Centimeter
    public const string CUBIC_DECIMETER  = 'DMQ' ; // Cubic Decimeter
    public const string US_GALLON        = 'GLD' ; // US Gallon
    public const string IMPERIAL_GALLON  = 'GLI' ; // Imperial Gallon (UK)
    public const string BARREL           = 'BLL' ; // Barrel
    public const string CUBIC_FOOT       = 'FTQ' ; // Cubic Foot

    // =====================================================================
    // Time Units
    // =====================================================================

    public const string DAY    = 'DAY' ; // Day
    public const string HOUR   = 'HUR' ; // Hour
    public const string MINUTE = 'MIN' ; // Minute
    public const string SECOND = 'SEC' ; // Second
    public const string MONTH  = 'MON' ; // Month
    public const string YEAR   = 'ANN' ; // Year
    public const string WEEK   = 'WEE' ; // Week

    // =====================================================================
    // Percentage and Ratio Units
    // =====================================================================

    public const string PERCENT           = 'PC1' ; // Percent
    public const string PER_THOUSAND      = 'PER' ; // Per thousand
    public const string PARTS_PER_MILLION = 'PPM' ; // Parts per million

    // =====================================================================
    // Energy Units
    // =====================================================================

    public const string KILOWATT_HOUR = 'KWH' ; // Kilowatt hour
    public const string JOULE         = 'JOU' ; // Joule
    public const string KILOJOULE     = 'KJO' ; // Kilojoule
    public const string CALORIE       = 'CAL' ; // Calorie (large calorie)
    public const string KILOCALORIE   = 'KCC' ; // Kilocalorie

    // =====================================================================
    // Pressure Units
    // =====================================================================

    public const string PASCAL                = 'PAL' ; // Pascal
    public const string BAR                   = 'BAR' ; // Bar
    public const string MILLIBAR              = 'MBR' ; // Millibar
    public const string POUND_PER_SQUARE_INCH = 'PSI' ; // Pound per square inch

    // =====================================================================
    // Temperature Units
    // =====================================================================

    public const string CELSIUS    = 'CEL' ; // Degree Celsius
    public const string FAHRENHEIT = 'FAH' ; // Degree Fahrenheit
    public const string KELVIN     = 'KEL' ; // Kelvin

    // =====================================================================
    // Generic / Dimensionless Units & Factors
    // =====================================================================

    public const string COUNT           = 'CBR' ; // Count (similar to NMB, often for specific items)
    public const string NUMBER          = 'NMB' ; // Number / Count / Factor (dimensionless)
    public const string RATIO           = 'RTO' ; // Ratio (often used for dimensionless quantities)
    public const string UNIT_OF_CAPITAL = 'UC'  ; // Unit of Capital (for monetary units, though priceCurrency is better for actual currency)
    public const string SCORE           = 'SCO' ; // Score (for points or a numerical rating)
    public const string POINT           = 'PT'  ; // Point (e.g., a single point in a system)

    // =====================================================================
    // Degrees Units
    // =====================================================================

    public const string ANGULAR = 'DD'  ; // Angular Degree
    public const string RADIAN  = 'RAD' ; // Radian

    // =====================================================================
    // Common Miscellaneous Units
    // =====================================================================

    public const string AMPERE                = 'AMP'; // Ampere
    public const string BECQUEREL             = 'BQL'; // Becquerel
    public const string BIT                   = 'BIT'; // Bit
    public const string BYTE                  = 'BTE'; // Byte
    public const string COULOMB               = 'CLB'; // Coulomb
    public const string DECIBEL               = 'DB';  // Decibel
    public const string FARAD                 = 'FAR'; // Farad
    public const string GIGABYTE              = 'GB';  // Gigabyte (Pas de code UN/CEFACT direct, mais symbole courant)
    public const string GRAY                  = 'GRY'; // Gray
    public const string HENRY                 = 'HNH'; // Henry
    public const string HERTZ                 = 'HTZ'; // Hertz
    public const string KILOHERTZ             = 'KHZ'; // Kilohertz
    public const string KILOBYTE              = 'KB';  // Kilobyte (Code UN/CEFACT corrigé)
    public const string KILOWATT              = 'KWT'; // Kilowatt
    public const string LUMEN                 = 'LUM'; // Lumen
    public const string LUX                   = 'LUX'; // Lux
    public const string MEGAHERTZ             = 'MHZ'; // Megahertz
    public const string MEGABYTE              = 'MB';  // Megabyte (Code UN/CEFACT corrigé)
    public const string NEWTON                = 'NEW'; // Newton
    public const string OHM                   = 'OHM'; // Ohm
    public const string POUND_FORCE           = 'LBF'; // Pound-force
    public const string REVOLUTION_PER_MINUTE = 'RPM'; // Revolution per minute
    public const string SIEVERT               = 'SVT'; // Sievert
    public const string SIEMENS               = 'SIE'; // Siemens
    public const string TESLA                 = 'TSL'; // Tesla
    public const string VOLT                  = 'VLT'; // Volt
    public const string WATT                  = 'WTT'; // Watt
    public const string WEBER                 = 'WEB'; // Weber

    // =====================================================================
    // Private
    // =====================================================================

    private static ?array $NAMES   = null ;
    private static ?array $SYMBOLS = null ;

    // =====================================================================
    // Methods
    // =====================================================================

    /**
     * Returns the UnitCode definition (name, unitCode and unitText) of the specific code
     * or null if the code is not valid.
     * @param string $code
     * @return ?PropertyValue
     */
    public static function get( string $code ): ?PropertyValue
    {
        if( self::isValid( $code ) )
        {
            return new PropertyValue
            ([
                Prop::NAME       => self::getName( $code ) ,
                Prop::UNIT_CODE  => $code ,
                Prop::UNIT_TEXT  => self::getSymbol( $code ) ,
            ] );
        }
        return null ;
    }

    /**
     * Returns the code with a specific unit code name.
     * @param string $name
     * @return string|null
     */
    public static function getFromName( string $name ): ?string
    {
        return MeasureName::getCode( $name ) ;
    }

    /**
     * Returns the code from a specific unit code symbol.
     * @param string $symbol
     * @return string|null
     */
    public static function getFromSymbol( string $symbol ): ?string
    {
        return MeasureSymbol::getCode( $symbol ) ;
    }

    /**
     * Returns the official UN/CEFACT name for a given code.
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

    /**
     * Returns the official UN/CEFACT symbol for a given code.
     * @param string $code
     * @return string|null The UN/CEFACT code (e.g., 'KGM') or null if not found.
     */
    public static function getSymbol( string $code ): ?string
    {
        if( static::$SYMBOLS === null )
        {
            static::$SYMBOLS = MeasureSymbol::getAll() ;
        }
        return static::$SYMBOLS[ self::getConstant( $code ) ] ?? null;
    }
}