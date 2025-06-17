<?php

namespace org\schema\services;

use org\schema\MonetaryAmount;

/**
 * A payment method using a credit, debit, store or other card to associate the payment with an account.
 * @see https://schema.org/PaymentCard
 */
class PaymentCard extends FinancialProduct
{
    /**
     * A cardholder benefit that pays the cardholder a small percentage of their net expenditures.
     * @var int|float|bool|null
     */
    public null|int|float|bool $cashBack ;

    /**
     * A secure method for consumers to purchase products or services via debit, credit or smartcards by using RFID or NFC technology.
     * @var bool|null
     */
    public ?bool $contactlessPayment ;

    /**
     * A floor limit is the amount of money above which credit card transactions must be authorized.
     * @var MonetaryAmount|null
     */
    public ?MonetaryAmount $floorLimit ;

    /**
     * The minimum payment is the lowest amount of money that one is required to pay on a credit card statement each month.
     * @var int|float|MonetaryAmount|null
     */
    public null|int|float|MonetaryAmount $monthlyMinimumRepaymentAmount ;
}