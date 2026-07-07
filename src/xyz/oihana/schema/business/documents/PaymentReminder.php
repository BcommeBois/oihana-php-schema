<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\enumerations\status\ActionStatusType;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\PaymentReminderTrait;
use xyz\oihana\schema\enumerations\PaymentReminderChannel;
use xyz\oihana\schema\enumerations\PaymentReminderLevel;

/**
 * The record of a payment reminder sent to (or planned for) the customer
 * about an unpaid {@see PaymentInstallment} or {@see BusinessDocument}.
 *
 * A pure trace, not an engine : it captures what was sent (date, level,
 * channel, claimed amount, applied charges) — never the rules that decide
 * *when* to remind, the escalation logic or the actual sending, which are
 * consumer-side concerns.
 *
 * The late-payment charges a reminder carries (interest, a fixed recovery
 * fee...) are expressed as {@see Adjustment}, the same vehicle already used
 * on lines and documents — never a bespoke "penalty" field. These stay
 * descriptive of the reminder ; whether they roll into the document
 * {@see DocumentTotals} is a consumer decision.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class PaymentReminder extends StructuredValue
{
    use PaymentReminderTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The late-payment charges carried by this reminder (interest, a fixed
     * recovery fee...), expressed as {@see Adjustment} — the same vehicle
     * used elsewhere in this namespace, never a bespoke "penalty" field.
     * @var null|array|Adjustment
     */
    #[HydrateWith(Adjustment::class)]
    public null|array|Adjustment $adjustments ;

    /**
     * The amount claimed by this reminder (typically the outstanding balance).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amountClaimed ;

    /**
     * The channel the reminder is sent through. Reuses
     * {@see PaymentReminderChannel} (email, postal, phone, SMS...) or a plain
     * free-text label.
     * @var null|string|PaymentReminderChannel
     */
    public null|string|PaymentReminderChannel $channel ;

    /**
     * The date the reminder was sent, or is planned to be sent.
     * @var string|int|null
     */
    public null|string|int $date ;

    /**
     * The escalation level of the reminder. Reuses {@see PaymentReminderLevel}
     * (from a courteous reminder to a formal notice) or a plain free-text label.
     * @var null|string|PaymentReminderLevel
     */
    public null|string|PaymentReminderLevel $level ;

    /**
     * A free-text note about the reminder (e.g. an internal reference).
     * @var string|null
     */
    public ?string $note ;

    /**
     * The processing status of the reminder — planned, sent or handled.
     * Reuses {@see ActionStatusType} and its existing member classes
     * ({@see \org\schema\enumerations\status\PotentialActionStatus},
     * `ActiveActionStatus`, `CompletedActionStatus`) rather than a new
     * enumeration.
     * @var null|string|ActionStatusType
     */
    public null|string|ActionStatusType $status ;
}
