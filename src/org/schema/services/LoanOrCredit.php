<?php

namespace org\schema\services;

use org\schema\DefinedTerm;
use org\schema\Duration;
use org\schema\MonetaryAmount;
use org\schema\QuantitativeValue;
use org\schema\RepaymentSpecification;
use org\schema\Thing;

/**
 * A financial product for the loaning of an amount of money, or line of credit, under agreed terms and charges.
 * @see https://schema.org/LoanOrCredit
 */
class LoanOrCredit extends FinancialProduct
{
    /**
     * The amount of money.
     * @var int|float|MonetaryAmount|null
     */
    public null|int|float|MonetaryAmount $amount ;

    /**`
     * The currency in which the monetary amount is expressed.
     * Use standard formats: ISO 4217 currency format, e.g. "USD"; Ticker symbol for cryptocurrencies, e.g. "BTC"; well known names for Local Exchange Trading Systems (LETS) and other currency types, e.g. "Ithaca HOUR".
     * @var string|null
     */
    public ?string $currency ;

    /**
     * The period of time after any due date that the borrower has to fulfil its obligations before a default (failure to pay) is deemed to have occurred.
     * @var int|Duration|null
     */
    public null|int|Duration $gracePeriod ;

    /**
     * A form of paying back money previously borrowed from a lender.
     * Repayment usually takes the form of periodic payments that normally include part principal plus interest in each payment.
     * @var null|RepaymentSpecification
     */
    public ?RepaymentSpecification $loanRepaymentForm ;

    /**
     * The duration of the loan or credit agreement.
     * @var QuantitativeValue|null
     */
    public ?QuantitativeValue $loanTerm ;

    /**
     * The type of a loan or credit.
     * @var null|string|DefinedTerm
     */
    public null|string|DefinedTerm $loanType ;

    /**
     * The only way you get the money back in the event of default is the security.
     * Recourse is where you still have the opportunity to go back to the borrower for the rest of the money.
     * @var bool|null
     */
    public ?bool $recourseLoan ;

    /**
     * Whether the terms for payment of interest can be renegotiated during the life of the loan.
     * @var bool|null
     */
    public ?bool $renegotiableLoan ;

    /**
     * Assets required to secure loan or credit repayments. It may take form of third party pledge, goods, financial instruments (cash, securities, etc.)
     * @var null|string|Thing
     */
    public null|string|Thing $requiredCollateral ;
}