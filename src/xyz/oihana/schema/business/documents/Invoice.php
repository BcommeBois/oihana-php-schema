<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\CategoryCode;
use org\schema\Duration;
use org\schema\MonetaryAmount;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Thing;
use org\schema\enumerations\status\PaymentStatusType;

use xyz\oihana\schema\constants\traits\business\documents\InvoiceTrait;

/**
 * An invoice — the final billing document of the quote → purchase order →
 * invoice cycle, issued once the goods or services have been delivered.
 *
 * Reuses Schema.org's own `Invoice` property names (`accountId`,
 * `billingPeriod`, `broker`, `category`, `confirmationNumber`,
 * `paymentDueDate`, `paymentStatus`, `provider`, `referencesOrder`,
 * `scheduledPaymentDate`) rather than inventing new ones, per this
 * namespace's Schema.org-first naming rule (see {@see Quote::$validThrough}).
 *
 * Deliberately does NOT share a property trait with {@see \org\schema\Invoice},
 * even though both classes hydrate through the same
 * {@see \org\schema\traits\ThingTrait::__construct} raw-array path and are
 * therefore equally subject to the `null|array|X` typing rule documented on
 * {@see BusinessDocument}. Two of the candidate properties block a
 * genuinely shared trait:
 * - `referencesOrder` must point at this namespace's own {@see PurchaseOrder}
 *   (the business document actually being invoiced), not `org\schema\Order`
 *   — the two `Invoice` classes need incompatible types for the same
 *   property name.
 * - The mirror's `broker`/`category`/`billingPeriod` unions predate the
 *   `null|array|X` convention and lack `array`; widening them to fit a
 *   shared trait would edit `org\schema\Invoice`, contradicting this
 *   namespace's "mirror stays untouched" rule (see {@see BusinessDocument}).
 *
 * The properties are therefore declared directly here, correctly typed,
 * rather than forcing a partial/asymmetric trait for no real benefit.
 *
 * `$paymentStatus` reuses {@see PaymentStatusType} and its existing member
 * classes ({@see \org\schema\enumerations\status\PaymentComplete},
 * `PaymentDue`, `PaymentDeclined`, `PaymentPastDue`,
 * `PaymentAutomaticallyApplied`) rather than new constants, following the
 * one-class-per-member convention of the `StatusEnumeration` family. The value
 * is written with the constants of {@see PaymentStatusType} — the schema.org
 * URL of the term (`https://schema.org/PaymentPastDue`) —, never with the name
 * of a member class.
 *
 * 🔑 **The state of payment is a reading, and it is dated.** The invoice does
 * not change once issued ; whether it is paid does. `paymentStatus`,
 * `paymentDueDate`, `totalPaymentDue` (what is left to pay) and
 * `paymentStatusDate` (the day they were read at their source) travel
 * together : an amount left to pay without the day it was read cannot tell a
 * reader whether it still holds.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class Invoice extends BusinessDocument
{
    use InvoiceTrait ;

    /**
     * The identifier for the account the payment will be applied to.
     * @var string|null
     */
    public ?string $accountId ;

    /**
     * The time interval used to compute the invoice.
     * @var null|array|Duration|int|float
     */
    public null|array|Duration|int|float $billingPeriod ;

    /**
     * An entity that arranges for an exchange between a buyer and a seller.
     * In most cases a broker never acquires or releases ownership of a product or service involved in an exchange.
     * @var null|array|Organization|Person
     */
    #[HydrateWith(Organization::class, Person::class)]
    public null|array|Organization|Person $broker ;

    /**
     * A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     * @var null|array|string|CategoryCode|Thing
     */
    public null|array|string|CategoryCode|Thing $category ;

    /**
     * A number that confirms the given order or payment has been received.
     * @var string|int|null
     */
    public null|string|int $confirmationNumber ;

    /**
     * The date that payment is due.
     * @var string|int|null
     */
    public null|string|int $paymentDueDate ;

    /**
     * The status of payment; whether the invoice has been paid or not.
     * @var null|string|PaymentStatusType
     */
    public null|string|PaymentStatusType $paymentStatus ;

    /**
     * The day the state of payment was read at its source — the books, a payment
     * provider —, as `Y-m-d`.
     *
     * It dates {@see Invoice::$paymentStatus}, {@see Invoice::$paymentDueDate} and
     * {@see Invoice::$totalPaymentDue} together : a payment received after that
     * day is not in them.
     *
     * @var string|null
     * @since 1.5.0
     */
    public ?string $paymentStatusDate ;

    /**
     * The service provider, service operator, or service performer; the goods producer.
     * Another party (a seller) may offer those services or goods on behalf of the provider, who may also serve as the seller.
     * @var null|array|Organization|Person
     */
    #[HydrateWith(Organization::class, Person::class)]
    public null|array|Organization|Person $provider ;

    /**
     * The delivery note(s) this invoice bills. One or more notes may be combined
     * into a single invoice.
     *
     * 🔑 **Often the only way to the invoice's lines.** An end-of-month invoice
     * is built from what left the warehouse during the period, and a source that
     * stamps the invoice number on the delivery — not on each line — leaves this
     * link as the sole path back to what was actually billed.
     *
     * The sibling of {@see Invoice::$referencesOrder}, and not a substitute for
     * it : orders say what was committed to, deliveries say what went out. An
     * order can be invoiced across two months, and one invoice can settle
     * deliveries belonging to several orders — neither reference alone describes
     * that.
     *
     * @var null|array|DeliveryNote
     * @since 1.4.0
     */
    #[HydrateWith(DeliveryNote::class)]
    public null|array|DeliveryNote $referencesDeliveryNote ;

    /**
     * The purchase order(s) this invoice bills. One or more purchase orders may be combined into a single invoice.
     * @var null|array|PurchaseOrder
     */
    #[HydrateWith(PurchaseOrder::class)]
    public null|array|PurchaseOrder $referencesOrder ;

    /**
     * The date the invoice is scheduled to be paid.
     * @var string|int|null
     */
    public null|string|int $scheduledPaymentDate ;

    /**
     * The total amount left to pay on the invoice, taxes included, as its source
     * stated it on {@see Invoice::$paymentStatusDate}.
     *
     * 🔑 **Not {@see DocumentTotals::$balanceDue}.** The balance of the totals is a
     * figure of the document itself — the total less the prepaid amount — and does
     * not move once the invoice is issued. This one moves with every payment, and
     * may be lower than the total when the invoice is partly paid, or negative
     * when the buyer is owed money.
     *
     * @var null|array|MonetaryAmount
     * @since 1.5.0
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $totalPaymentDue ;
}
