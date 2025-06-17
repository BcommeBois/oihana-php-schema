<?php

namespace org\schema\constants\uncefact;

use org\schema\constants\properties\Prop;
use org\schema\PropertyValue;
use oihana\reflections\traits\ConstantsTrait;

/**
 * UN/CEFACT Unit of Measure Names, Symbols, and Codes Class (Recommendation 20).
 *
 * This class provides an extended selection of the most commonly used codes
 * across various commercial and logistical contexts.
 *
 * For an exhaustive list and official documentation, please consult:
 * @see https://unece.org/trade/uncefact/cl-recommendations
 * @example
 * ```
 * <?php
 * $logger->info( json_encode( MeasureNames::PERCENT ) );
 * $logger->info( json_encode( MeasureNames::getCode( UnitNames::PERCENT ) ) );
 * $logger->info( json_encode( MeasureNames::getSymbol( UnitNames::PERCENT ) ) );
 * $logger->info( json_encode( MeasureNames::get( UnitNames::PERCENT ) ) );
 * ```
 */
class MeasureName
{
    use ConstantsTrait ;

    // =====================================================================
    // Quantity Units
    // =====================================================================

    public const string PIECE     = 'Piece' ;
    public const string UNIT      = 'Unit' ; // "Each" ou "Unit"
    public const string PAIR      = 'Pair' ;
    public const string DOZEN     = 'Dozen' ;
    public const string GROSS     = 'Gross' ;
    public const string HUNDRED   = 'Hundred' ;
    public const string THOUSAND  = 'Thousand' ;
    public const string TEN_PAIRS = 'Ten Pairs' ;

    // =====================================================================
    // Mass Units (Weight)
    // =====================================================================

    public const string KILOGRAM   = 'Kilogram';
    public const string GRAM       = 'Gram';
    public const string MILLIGRAM  = 'Milligram';
    public const string METRIC_TON = 'Metric Ton';
    public const string POUND      = 'Pound';
    public const string OUNCE      = 'Ounce';
    public const string CARAT      = 'Carat';

    // =====================================================================
    // Length Units
    // =====================================================================

    public const string METER      = 'Meter' ;
    public const string CENTIMETER = 'Centimeter' ;
    public const string HECTOMETER = 'Hectometer' ;
    public const string MILLIMETER = 'Millimeter' ;
    public const string KILOMETER  = 'Kilometer' ;
    public const string INCH       = 'Inch' ;
    public const string FOOT       = 'Foot' ;
    public const string YARD       = 'Yard' ;
    public const string MILE       = 'Mile' ;

    // =====================================================================
    // Area Units
    // =====================================================================

    public const string ACRE              = 'Acre';
    public const string ACRE_FOOT         = 'Acre-Foot'; // Note: This is typically a volume unit.
    public const string HECTARE           = 'Hectare';
    public const string SQUARE_CENTIMETER = 'Square Centimeter';
    public const string SQUARE_DECIMETER  = 'Square Decimeter';
    public const string SQUARE_FOOT       = 'Square Foot';
    public const string SQUARE_INCH       = 'Square Inch';
    public const string SQUARE_KILOMETER  = 'Square Kilometer';
    public const string SQUARE_METER      = 'Square Meter';
    public const string SQUARE_MILLIMETER = 'Square Millimeter';
    public const string SQUARE_MILE       = 'Square Mile';
    public const string SQUARE_YARD       = 'Square Yard';

    // =====================================================================
    // Volume Units
    // =====================================================================

    public const string BARREL           = 'Barrel';
    public const string CUBIC_FOOT       = 'Cubic Foot';
    public const string CUBIC_METER      = 'Cubic Meter';
    public const string CUBIC_CENTIMETER = 'Cubic Centimeter';
    public const string CUBIC_DECIMETER  = 'Cubic Decimeter';
    public const string IMPERIAL_GALLON  = 'Imperial Gallon';
    public const string LITER            = 'Liter';
    public const string MILLILITER       = 'Milliliter';
    public const string US_GALLON        = 'US Gallon';

    // =====================================================================
    // Time Units
    // =====================================================================

    public const string DAY    = 'Day'    ;
    public const string HOUR   = 'Hour'   ;
    public const string MINUTE = 'Minute' ;
    public const string SECOND = 'Second' ;
    public const string MONTH  = 'Month'  ;
    public const string YEAR   = 'Year'   ;
    public const string WEEK   = 'Week'   ;

    // =====================================================================
    //  Percentage and Ratio Units
    // =====================================================================

    public const string PERCENT           = 'Percent' ;
    public const string PER_THOUSAND      = 'Per Thousand' ;
    public const string PARTS_PER_MILLION = 'Parts Per Million' ;

    // =====================================================================
    // Energy Units
    // =====================================================================

    public const string CALORIE       = 'Calorie' ;
    public const string JOULE         = 'Joule' ;
    public const string KILOCALORIE   = 'Kilocalorie' ;
    public const string KILOJOULE     = 'Kilojoule' ;
    public const string KILOWATT_HOUR = 'Kilowatt-Hour' ;

    // =====================================================================
    // Pressure Units
    // =====================================================================

    public const string PASCAL                = 'Pascal';
    public const string BAR                   = 'Bar';
    public const string MILLIBAR              = 'Millibar';
    public const string POUND_PER_SQUARE_INCH = 'Pound per Square Inch';

    // =====================================================================
    // Temperature Units
    // =====================================================================

    public const string CELSIUS    = 'Degree Celsius';
    public const string FAHRENHEIT = 'Degree Fahrenheit';
    public const string KELVIN     = 'Kelvin';

    // =====================================================================
    // Generic / Dimensionless Units & Factors
    // =====================================================================

    public const string COUNT           = 'Count';
    // public const string DIMENSION_LESS  = 'Dimensionless'; // Use UNIT
    public const string NUMBER          = 'Number';
    public const string RATIO           = 'Ratio';
    public const string UNIT_OF_CAPITAL = 'Unit of Capital';
    public const string SCORE           = 'Score';
    public const string POINT           = 'Point';

    // =====================================================================
    // Degrees Units
    // =====================================================================

    public const string ANGULAR_DEGREE = 'Angular Degree';
    public const string RADIAN         = 'Radian';

    // =====================================================================
    // Common Miscellaneous Units
    // =====================================================================

    public const string AMPERE                = 'Ampere';
    public const string BECQUEREL             = 'Becquerel';
    public const string BIT                   = 'Bit';
    public const string BYTE                  = 'Byte';
    public const string COULOMB               = 'Coulomb';
    public const string DECIBEL               = 'Decibel';
    public const string FARAD                 = 'Farad';
    public const string GIGABYTE              = 'Gigabyte';
    public const string GRAY                  = 'Gray';
    public const string HENRY                 = 'Henry';
    public const string HERTZ                 = 'Hertz';
    public const string KILOHERTZ             = 'Kilohertz';
    public const string KILOBYTE              = 'Kilobyte';
    public const string KILOWATT              = 'Kilowatt';
    public const string LUMEN                 = 'Lumen';
    public const string LUX                   = 'Lux';
    public const string MEGAHERTZ             = 'Megahertz';
    public const string MEGABYTE              = 'Megabyte';
    public const string NEWTON                = 'Newton';
    public const string OHM                   = 'Ohm';
    public const string POUND_FORCE           = 'Pound-force';
    public const string REVOLUTION_PER_MINUTE = 'Revolution per Minute';
    public const string SIEVERT               = 'Sievert';
    public const string SIEMENS               = 'Siemens';
    public const string TESLA                 = 'Tesla';
    public const string VOLT                  = 'Volt';
    public const string WATT                  = 'Watt';
    public const string WEBER                 = 'Weber';

    // =====================================================================
    // Private
    // =====================================================================

    private static ?array $CODES   = null ;
    private static ?array $SYMBOLS = null ;

    // =====================================================================
    // Methods
    // =====================================================================

    /**
     * Returns the UnitCode definition (name, unitCode and unitText) of the specific name
     * or null if the name is not valid.
     * @param string $name The name of the unit.
     * @return ?PropertyValue
     */
    public static function get( string $name ): ?PropertyValue
    {
        if( self::isValid( $name ) )
        {
            return new PropertyValue
            ([
                Prop::NAME       => $name ,
                Prop::UNIT_CODE  => self::getCode( $name ) ,
                Prop::UNIT_TEXT  => self::getSymbol( $name ) ,
            ] );
        }
        return null ;
    }

    /**
     * Returns the official UN/CEFACT code for a given code.
     * @param string $name
     * @return string|null The UN/CEFACT code or null if not found.
     */
    public static function getCode( string $name ): ?string
    {
        if( static::$CODES === null )
        {
            static::$CODES = MeasureCode::getAll() ;
        }
        return static::$CODES[ self::getConstant( $name ) ] ?? null;
    }

    /**
     * Returns the name with a specific unit code.
     * @param string $code
     * @return string|null
     */
    public static function getFromCode( string $code ): ?string
    {
        return MeasureCode::getName( $code ) ;
    }

    /**
     * Returns the name from a specific unit code symbol.
     * @param string $symbol
     * @return string|null
     */
    public static function getFromSymbol( string $symbol ): ?string
    {
        return MeasureSymbol::getName( $symbol ) ;
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