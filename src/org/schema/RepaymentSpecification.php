<?php

namespace org\schema;

/**
 * A structured value representing repayment.
 * @see https://schema.org/RepaymentSpecification
 */
class RepaymentSpecification extends StructuredValue
{
    /**
     * A type of payment made in cash during the onset of the purchase of an expensive good/service.
     * The payment typically represents only a percentage of the full purchase price.
     * @var int|float|MonetaryAmount|null
     */
    public null|int|float|MonetaryAmount $downPayment ;

    /**
     * The amount to be paid as a penalty in the event of early payment of the loan.
     * @var MonetaryAmount|null
     */
    public ?MonetaryAmount $earlyPrepaymentPenalty ;

    /**
     * The amount of money to pay in a single payment.
     * @var MonetaryAmount|null
     */
    public ?MonetaryAmount $loanPaymentAmount ;

    /**
     * Frequency of payments due, i.e. number of months between payments.
     * This is defined as a frequency, i.e. the reciprocal of a period of time.
     * @var int|float|null
     */
    public null|int|float $loanPaymentFrequency ;

    /**
     * The number of payments contractually required at origination to repay the loan.
     * For monthly paying loans this is the number of months from the contractual first payment date to the maturity date.
     * @var int|float|null
     */
    public null|int|float $numberOfLoanPayments ;
}