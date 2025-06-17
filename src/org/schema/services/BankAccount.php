<?php

namespace org\schema\services;

use org\schema\DefinedTerm;
use org\schema\MonetaryAmount;

/**
 * A product or service offered by a bank whereby one may deposit, withdraw or transfer money and in some cases be paid interest.
 * @see https://schema.org/BankAccount
 */
class BankAccount extends FinancialProduct
{
    /**
     * A minimum amount that has to be paid in every month.
     * @var null|MonetaryAmount
     */
    public ?MonetaryAmount $accountMinimumInflow ;

    /**
     * An overdraft is an extension of credit from a lending institution when an account reaches zero.
     * An overdraft allows the individual to continue withdrawing money even if the account has no funds in it.
     * Basically the bank allows people to borrow a set amount of money.
     * @var null|MonetaryAmount
     */
    public ?MonetaryAmount $accountOverdraftLimit ;

    /**
     * The type of a bank account.
     * @var null|String|DefinedTerm
     */
    public null|String|DefinedTerm $bankAccountType ;
}