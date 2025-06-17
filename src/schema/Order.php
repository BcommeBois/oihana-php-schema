<?php

namespace org\schema;

use DateTime;
use org\schema\enumerations\status\OrderStatus;

/**
 * An order is a confirmation of a transaction (a receipt), which can contain multiple line items, each represented by an Offer that has been accepted by the customer.
 * @see https://schema.org/Order
 */
class Order extends Intangible
{
    /**
     * The offer(s) -- e.g., product, quantity and price combinations -- included in the order.
     * @var array|Offer|null
     */
    public null|array|Offer $acceptedOffer ;

    /**
     * The billing address for the order.
     * @var PostalAddress|null
     */
    public null|PostalAddress $billingAddress ;

    /**
     * An entity that arranges for an exchange between a buyer and a seller.
     * In most cases a broker never acquires or releases ownership of a product or service involved in an exchange.
     * If it is not clear whether an entity is a broker, seller, or buyer, the latter two terms are preferred.
     * @var Organization|Person|null
     */
    public null|Organization|Person $broker ;

    /**
     * A number that confirms the given order or payment has been received.
     * @var string|int|null
     */
    public null|string|int $confirmationNumber ;

    /**
     * Party placing the order or paying the invoice.
     * @var Organization|Person|null
     */
    public null|Organization|Person $customer ;

    /**
     * Any discount applied (to an Order).
     * @var int|float|string|null
     */
    public null|int|float|string $discount ;

    /**
     * Code used to redeem a discount.
     * @var string|null
     */
    public ?string $discountCode ;

    /**
     * The currency of the discount.
     * Use standard formats: ISO 4217 currency format, e.g. "USD"; Ticker symbol for cryptocurrencies, e.g. "BTC"; well known names for Local Exchange Trading Systems (LETS) and other currency types, e.g. "Ithaca HOUR".
     */
    public ?string $discountCurrency ;

    /**
     * Indicates whether the offer was accepted as a gift for someone other than the buyer.
     * @var bool|null
     */
    public ?bool $isGift;

    /**
     * Date order was placed.
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $orderDate ;

    /**
     * The delivery of the parcel related to this order or order item.
     * @var null|ParcelDelivery
     */
    public ?ParcelDelivery $orderDelivery ;

    /**
     * The identifier of the transaction.
     * @var int|string|null
     */
    public null|int|string $orderNumber ;

    /**
     * The current status of the order.
     * @var string|DefinedTerm|OrderStatus|null
     */
    public null|string|DefinedTerm|OrderStatus $orderStatus ;

    /**
     * The item ordered.
     * @var array|Product|Service|OrderItem|null
     */
    public null|array|Product|Service|OrderItem $orderedItem ;

    /**
     * The order is being paid as part of the referenced Invoice.
     * @var array|Invoice|null
     */
    public null|array|Invoice $partOfInvoice ;

    /**
     * The date that payment is due.
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $paymentDueDate ;

    /**
     * The name of the credit card or other method of payment for the order.
     * @var PaymentMethod|string|null
     */
    public null|PaymentMethod|string $paymentMethod;

    /**
     * An identifier for the method of payment used (e.g. the last 4 digits of the credit card).
     * @var string|null
     */
    public ?string $paymentMethodId ;

    /**
     * The URL for sending a payment.
     * @var string|null
     */
    public ?string $paymentUrl ;

    /**
     * An entity which offers (sells / leases / lends / loans) the services / goods. A seller may also be a provider.
     * @var Organization|Person|null
     */
    public null|Organization|Person $seller ;
}