<?php

namespace org\schema\services;

use org\schema\QuantitativeValue;
use org\schema\Service;

/**
 * A product provided to consumers and businesses by financial institutions such as banks, insurance companies, brokerage firms, consumer finance companies, and investment companies which comprise the financial services industry.
 * @see https://schema.org/FinancialProduct
 */
class FinancialProduct extends Service
{
    /**
     * The annual rate that is charged for borrowing (or made by investing), expressed as a single percentage number that represents the actual yearly cost of funds over the term of a loan.
     * This includes any fees or additional costs associated with the transaction.
     * @var int|float|QuantitativeValue|null
     */
    public null|int|float|QuantitativeValue $annualPercentageRate ;

    /**
     * Description of fees, commissions, and other terms applied either to a class of financial product, or by a financial service organization.
     * @var string|null
     */
    public ?string $feesAndCommissionsSpecification ;

    /**
     * The interest rate, charged or paid, applicable to the financial product.
     * Note: This is different from the calculated annualPercentageRate.
     * @var int|float|QuantitativeValue|null
     */
    public null|int|float|QuantitativeValue $interestRate ;
}