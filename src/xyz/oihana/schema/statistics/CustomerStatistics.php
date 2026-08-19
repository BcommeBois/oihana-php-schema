<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Person;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\CustomerStatisticsTrait;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\traits\HasTradingMeasures;

/**
 * What one customer traded over one year.
 *
 * A {@see Statistics} record whose subject is a {@see Customer}, carrying the
 * ten measures of {@see HasTradingMeasures} : what the customer bought and what
 * it earned, at each of the three costs, with the margin over each, and what
 * physically moved. Its {@see Statistics::$direction} is a sale — the operator
 * is the seller.
 *
 * 🔑 **The two dimensions are the ones the customer had when the record was
 * written**, copied from the customer rather than joined at read time : who
 * carried the account ({@see CustomerStatistics::$assignedSeller}) and which
 * point of sale served it ({@see CustomerStatistics::$assignedPOS}). They are
 * what lets a reader group a portfolio or a branch without walking back to the
 * customer for every figure.
 *
 * ⚠️ **And they are a photograph, not a history.** A customer reassigned to
 * another salesperson takes its whole past with it at the next write : the
 * figures then read as *the customer's current owner's*, which is a true
 * statement and rarely the same one as *what that salesperson invoiced*. An
 * application showing them owes its reader the distinction ; sources that
 * attribute at invoicing time answer the other question, and belong in their own
 * records.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class CustomerStatistics extends Statistics
{
    use CustomerStatisticsTrait ,
        HasTradingMeasures      ,
        HasTradingMeasuresTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The customer these figures are about.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Customer}, a bare code is
     * left as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.4.0
     */
    #[HydrateAs(Customer::class)]
    public null|int|string|array|Thing $about ;

    /**
     * The point of sale the customer was served by.
     *
     * A code, or the resolved warehouse. Reuses the name, the shape and the
     * meaning {@see Customer::$assignedPOS} already carries.
     *
     * @var null|int|string|array|Warehouse
     * @since 1.4.0
     */
    public null|int|string|array|Warehouse $assignedPOS ;

    /**
     * The salesperson the customer was attached to.
     *
     * A code, or the resolved person. Reuses the name, the shape and the meaning
     * {@see Customer::$assignedSeller} already carries.
     *
     * @var null|int|string|array|Person
     * @since 1.4.0
     */
    public null|int|string|array|Person $assignedSeller ;
}
