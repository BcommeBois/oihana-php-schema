<?php

namespace xyz\oihana\schema\products;

use ReflectionException;

use oihana\reflect\attributes\HydrateWith;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\places\Warehouse;

/**
 * Represents the stock level information for a specific product within a given warehouse.
 *
 * This class extends {@see QuantitativeValue} to describe inventory quantities,
 * enriched with additional metadata required integrations such as:
 *
 * - The point of sale (POS) or warehouse associated with the stock.
 * - Dates of the last recorded stock entry and exit.
 *
 * It can be serialized to JSON-LD using the {@see CONTEXT} constant to provide
 * a semantic context aligned with the Oihana schema definitions.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class StockLevel extends QuantitativeValue
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The point of sale (POS) assigned to the customer.
     *
     * This may refer to a warehouse where the product is stored or a shop.
     *
     * @var string|array|Warehouse|null The identifier or object representing the POS.
     */
    #[HydrateWith(Warehouse::class)]
    public null|array|string|Warehouse $assignedPOS ;

    /**
     * The availability of this item—for example In stock, Out of stock, Pre-order, etc.
     * @var string|object|null
     */
    public string|object|null $availability ;

    /**
     * Date and time of the most recent stock entry (incoming inventory).
     *
     * Expected formats:
     * - A string in ISO 8601 format (e.g. "2025-01-12T14:32:00Z"),
     * - Or null if no stock entry has ever been recorded.
     *
     * @var string|null The date of the last incoming stock movement.
     */
    public null|string $lastStockEntry ;

    /**
     * Date and time of the most recent stock exit (outgoing inventory).
     *
     * Expected formats:
     * - A string in ISO 8601 format (e.g. "2025-01-12T14:32:00Z"),
     * - Or null if no stock exit has ever been recorded.
     *
     * @var string|null The date of the last outgoing stock movement.
     */
    public null|string $lastStockExit ;

    /**
     * Creates a new StockLevel instanceof with an array.
     * @throws ReflectionException
     */
    public static function fromArray( ?array $init = null  ):?StockLevel
    {
        $level = null ;

        if( is_array( $init ) )
        {
            $level = new StockLevel( $init ) ;
        }

        if( ( $level instanceof StockLevel ) )
        {
            $assignedPOS = $level->assignedPOS ?? null ;
            if( !( $assignedPOS instanceof Warehouse ) && is_array( $assignedPOS ) )
            {
                $level->assignedPOS = new Warehouse( $assignedPOS ) ;
            }
        }

        return $level instanceof StockLevel ? $level : null ;
    }
}