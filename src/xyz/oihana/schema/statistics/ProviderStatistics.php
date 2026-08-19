<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\traits\HasTradingMeasures;

/**
 * What was bought from one supplier over one year.
 *
 * A {@see Statistics} record whose subject is a {@see Provider}, carrying the
 * same ten measures as every other family ({@see HasTradingMeasures}). Its
 * {@see Statistics::$direction} is a purchase — the operator is the buyer.
 *
 * 🔑 **A purchase record fills fewer of them, and says so by leaving the rest
 * out.** What was bought has a cost and a quantity ; it has no revenue, and
 * therefore no margin — those belong to the sale that will follow, on the
 * customer's own record. A property left unset disappears from the document ;
 * writing zeros instead would state that nothing was earned, which is a
 * different claim and a false one.
 *
 * It carries no dimension of its own : a supplier is not attached to a
 * salesperson or to a point of sale the way a customer is. The company the goods
 * were bought for is on the record already, as
 * {@see Statistics::$assignedCompany}.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class ProviderStatistics extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The supplier these figures are about.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Provider}, a bare code is
     * left as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.4.0
     */
    #[HydrateAs(Provider::class)]
    public null|int|string|array|Thing $about ;
}
