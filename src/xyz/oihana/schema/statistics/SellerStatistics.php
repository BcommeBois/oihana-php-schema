<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\constants\traits\statistics\HasUninvoicedTradeTrait;
use xyz\oihana\schema\constants\traits\statistics\SellerStatisticsTrait;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\traits\HasTradingMeasures;
use xyz\oihana\schema\traits\HasUninvoicedTrade;

/**
 * What one salesperson traded over one year.
 *
 * A {@see Statistics} record whose subject is a {@see Seller}, carrying the same
 * ten measures as every other family ({@see HasTradingMeasures}). It answers the
 * question a salesperson is actually asked — *what did I sell* — and it is the
 * figure a {@see SalesObjectives} target is read against.
 *
 * Beside the ten measures, it may carry the trade that is not invoiced yet
 * ({@see HasUninvoicedTrade}) : what was delivered and is still to invoice
 * (`uninvoicedRevenue`) and its cost price (`uninvoicedCostPrice`), and what is
 * ordered and still to deliver (`orderBacklog`). `revenue` only sees a sale once
 * it is invoiced ; these series say how the month stands before that, `revenue`
 * plus `uninvoicedRevenue` is what was delivered, and `costPrice` plus
 * `uninvoicedCostPrice` what it cost. A record whose trade is all invoiced
 * leaves them unset.
 *
 * 🔑 **It is written at whatever grain its source attributes.** A source that
 * attributes each sale to one customer publishes one record per customer, named
 * by {@see SellerStatistics::$assignedCustomer} ; a source that totals the
 * salesperson leaves that property unset. Both are the same class, and a reader
 * tells them apart by whether the dimension is there.
 *
 * ⚠️ **Attribution is a choice, and two defensible ones disagree.** A source that
 * attributes at the moment of the sale credits whoever made it, for good. A
 * portfolio read from {@see CustomerStatistics::$assignedSeller} credits whoever
 * holds the account *now*, and moves a whole history along with the account. Both
 * are true sentences about different things, they part company at every transfer,
 * and no record can say which one a reader wanted — so whoever serves them owes
 * the distinction to whoever reads them.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class SellerStatistics extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ,
        HasUninvoicedTrade      ,
        HasUninvoicedTradeTrait ,
        SellerStatisticsTrait   ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The salesperson these figures are about.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Seller}, a bare code is left
     * as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.5.0
     */
    #[HydrateAs(Seller::class)]
    public null|int|string|array|Thing $about ;

    /**
     * The customer the figures were traded with.
     *
     * A code, or the resolved customer. Unset when the source totals the
     * salesperson rather than breaking the figures down customer by customer.
     *
     * @var null|int|string|array|Customer
     * @since 1.5.0
     */
    public null|int|string|array|Customer $assignedCustomer ;
}
