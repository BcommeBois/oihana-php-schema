<?php

namespace org\schema;

use DateTimeInterface;
use org\schema\enumerations\status\PaymentStatusType;

/**
 * A statement of the money due for goods or services; a bill.
 * @see https://schema.org/Invoice
 */
class Invoice extends Intangible
{
    /**
     * The identifier for the account the payment will be applied to.
     * @var string|null
     */
    public ?string $accountId ;

    /**
     * The time interval used to compute the invoice.
     * @var Duration|int|float|null
     */
    public null|Duration|int|float $billingPeriod ;

    /**
     * An entity that arranges for an exchange between a buyer and a seller.
     * In most cases a broker never acquires or releases ownership of a product or service involved in an exchange.
     * If it is not clear whether an entity is a broker, seller, or buyer, the latter two terms are preferred.
     * @var Organization|Person|null
     */
    public null|Organization|Person $broker ;

    /**
     * A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     * @see null|string|CategoryCode|Thing
     */
    public null|string|CategoryCode|Thing $category ;

    /**
     * A number that confirms the given order or payment has been received.
     * @var string|null
     */
    public ?string $confirmationNumber ;

    /**
     * A number that confirms the given order or payment has been received.
     * @var Organization|Person|null
     */
    public null|Person|Organization $customer ;

    /**
     * The minimum payment required at this time.
     * @var MonetaryAmount|PriceSpecification|null
     */
    public null|MonetaryAmount|PriceSpecification $minimumPaymentDue ;

    /**
     * The date that payment is due.
     * @var string|DateTimeInterface|null
     */
    public null|string|DateTimeInterface $paymentDueDate ;

    /**
     * The name of the credit card or other method of payment for the order.
     * @var null|string|PaymentMethod
     */
    public null|string|PaymentMethod $paymentMethod ;

    /**
     * An identifier for the method of payment used (e.g. the last 4 digits of the credit card).
     * @var string|null
     */
    public ?string $paymentMethodId ;

    /**
     * The status of payment; whether the invoice has been paid or not.
     * @var string|null|PaymentStatusType
     */
    public null|string|PaymentStatusType $paymentStatus ;

    /**
     * The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $provider ;

    /**
     * The Order(s) related to this Invoice.
     * One or more Orders may be combined into a single Invoice.
     * @var Order|null
     */
    public ?Order $referencesOrder;

    /**
     * The date the invoice is scheduled to be paid.
     * @var DateTimeInterface|string|null
     */
    public null|string|DateTimeInterface $scheduledPaymentDate ;

    /**
     * The total amount due.
     * @var MonetaryAmount|PriceSpecification|null
     */
    public null|MonetaryAmount|PriceSpecification $totalPaymentDue ;
}