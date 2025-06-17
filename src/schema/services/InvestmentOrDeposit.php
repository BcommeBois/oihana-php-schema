<?php

namespace org\schema\services;

use org\schema\MonetaryAmount;

/**
 * A type of financial product that typically requires the client to transfer funds to a financial service in return for potential beneficial financial return.
 * @see https://schema.org/InvestmentOrDeposit
 */
class InvestmentOrDeposit extends FinancialProduct
{
    /**
     * The amount of money.
     * @var int|float|MonetaryAmount|null
     */
    public null|int|float|MonetaryAmount $amount ;
}