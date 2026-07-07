<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\AgingSummaryTrait;

/**
 * The accounts-receivable aging breakdown of a {@see Statement} : how much of
 * the outstanding balance is still current versus overdue, split into the
 * industry-standard buckets (current, 1–30, 31–60, 61–90, over 90 days).
 *
 * A reporting convention expected of any "statement of account" (QuickBooks'
 * `CustomerBalanceDetail`, Xero's `AgedReceivablesByContact`), which UBL's own
 * `Statement` schema does not carry either. The library only models the shape :
 * the consumer computes each bucket (typically from each entry's due date),
 * this object stores the result — a value object, not an aging engine, in the
 * same spirit as {@see PaymentReminder}.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class AgingSummary extends StructuredValue
{
    use AgingSummaryTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The amount not yet overdue.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $current ;

    /**
     * The amount overdue by 1 to 30 days.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $days1To30 ;

    /**
     * The amount overdue by 31 to 60 days.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $days31To60 ;

    /**
     * The amount overdue by 61 to 90 days.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $days61To90 ;

    /**
     * The amount overdue by more than 90 days.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $over90 ;
}
