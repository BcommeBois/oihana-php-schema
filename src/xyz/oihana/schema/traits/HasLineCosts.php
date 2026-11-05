<?php

namespace xyz\oihana\schema\traits;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\UnitPriceSpecification;

/**
 * Provides what the goods of a line cost the operator who sells them.
 *
 * Three figures, read beside the selling price the line already states : the
 * cost price of one unit (`unitCostPrice`), the purchase price of one unit
 * (`unitPurchasePrice`), and the cost of the whole line (`costPrice`). From them
 * a reader works out the margin of the line — `subtotal − costPrice` —, its rate
 * and its coefficient, and the margin of the whole document by summing the lines.
 *
 * 🔑 **The cost of the whole line is stated, never left to the reader.** Unit
 * price × quantity is only right on the simplest line : goods sold by the package
 * are costed per unit of the tariff, a flat fee has no quantity, and a source may
 * correct a cost when it invoices. The line carries the amount the source works
 * out, and a reader adds amounts up.
 *
 * 🔑 **Absent and zero say two different things.** A zero is a cost the source
 * states — a fee that costs nothing to sell. An absent key says no cost is known,
 * or none is disclosed to this reader : a margin can then not be worked out, and
 * must not be shown as a whole one.
 *
 * ⚠️ **Confidential by nature**, like the costs of {@see HasTradingMeasures}. Read
 * beside the selling price, a cost price **is** a margin and a margin **is** a cost
 * price : a customer must never read them about its own documents. The purchase
 * price says more still — what a supplier charges. The library states the shape ;
 * the guard belongs to the consumer, which alone knows its readers, and should
 * open the cost price and the purchase price separately.
 *
 * Composed by {@see \xyz\oihana\schema\business\documents\BusinessDocumentLine}.
 * The companion property name constants live in
 * {@see \xyz\oihana\schema\constants\traits\business\documents\HasLineCostsTrait},
 * as {@see HasTradingMeasures} does with its own.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait HasLineCosts
{
    /**
     * What the goods of the whole line cost — the cost price of every unit sold
     * on it, as the source works it out.
     *
     * The amount a margin is read against : `subtotal − costPrice` is what the
     * line earns. Named as the cost price of a body of trades is named in
     * {@see HasTradingMeasures::$costPrice} : an amount, never a unit price.
     *
     * Example :
     * ```php
     * $line->costPrice = new MonetaryAmount([ 'value' => 1284.88 , 'currency' => 'EUR' ]) ;
     * ```
     *
     * @var null|array|MonetaryAmount
     * @since 1.5.0
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $costPrice ;

    /**
     * The cost price of one unit of the goods — what they cost once bought,
     * carried and stored — in the unit the selling price of the line is stated in.
     *
     * Stated per unit so that it reads beside the selling price : `25.40` a square
     * metre against a selling price of `30.50` a square metre. The amount of the
     * whole line is {@see HasLineCosts::$costPrice}.
     *
     * Example :
     * ```php
     * $line->unitCostPrice = new UnitPriceSpecification
     * ([
     *     'price'         => 25.40 ,
     *     'priceCurrency' => 'EUR' ,
     *     'unitCode'      => 'MTK' ,
     * ]);
     * ```
     *
     * @var null|array|UnitPriceSpecification
     * @since 1.5.0
     */
    #[HydrateAs(UnitPriceSpecification::class)]
    public null|array|UnitPriceSpecification $unitCostPrice ;

    /**
     * The purchase price of one unit of the goods — what the supplier charges,
     * after the discounts it grants — in the unit the selling price of the line is
     * stated in.
     *
     * ⚠️ More sensitive than the cost price : it discloses the terms of a supplier,
     * and is best opened to fewer readers.
     *
     * Example :
     * ```php
     * $line->unitPurchasePrice = new UnitPriceSpecification
     * ([
     *     'price'         => 24.10 ,
     *     'priceCurrency' => 'EUR' ,
     *     'unitCode'      => 'MTK' ,
     * ]);
     * ```
     *
     * @var null|array|UnitPriceSpecification
     * @since 1.5.0
     */
    #[HydrateAs(UnitPriceSpecification::class)]
    public null|array|UnitPriceSpecification $unitPurchasePrice ;
}
