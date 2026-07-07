<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\enumerations\status\PaymentStatusType;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\PaymentInstallmentTrait;

/**
 * A single scheduled payment within a {@see PaymentSchedule}.
 *
 * Carries either a fixed `amount` or a `percentage` of the document total —
 * whichever is known at the time the schedule is defined.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class PaymentInstallment extends StructuredValue
{
    use PaymentInstallmentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The amount due at this installment.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amount ;

    /**
     * The date this installment is due.
     * @var string|int|null
     */
    public null|string|int $dueDate ;

    /**
     * The payment status of this installment — whether it has been paid,
     * is still due or is past due. Reuses {@see PaymentStatusType} and its
     * existing member classes ({@see \org\schema\enumerations\status\PaymentComplete},
     * `PaymentDue`, `PaymentPastDue`...), the finer-grained counterpart of
     * {@see Invoice::$paymentStatus} at the level of a single installment.
     * @var null|string|PaymentStatusType
     */
    public null|string|PaymentStatusType $paymentStatus ;

    /**
     * The share of the document total this installment represents, as a percentage (e.g. 30 for 30%).
     * @var int|float|null
     */
    public null|int|float $percentage ;
}
