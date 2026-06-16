<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The role played by a {@see \xyz\oihana\schema\business\BusinessIdentity}.
 *
 * A *business identity* links an authenticated account to a business entity
 * (a {@see \org\schema\Person} or an {@see \org\schema\Organization}). This
 * enumeration qualifies the **nature** of that link, so a consumer can route
 * and authorize per role without inferring it from the linked entity type.
 *
 * The values are short, stable tokens — they are stored on the relationship
 * and used as discriminators in application logic.
 *
 * | Constant         | Description                                                       | Value             |
 * |------------------|-------------------------------------------------------------------|-------------------|
 * | CUSTOMER         | The account is (or directly represents) a customer organization.  | `customer`        |
 * | CUSTOMER_CONTACT | The account is a contact/employee acting for a customer.          | `customerContact` |
 * | DELIVERER        | The account is a deliverer / carrier.                             | `deliverer`       |
 * | PROVIDER         | The account is a provider / supplier.                            | `provider`        |
 * | SELLER           | The account is a seller / sales representative.                  | `seller`          |
 *
 * @package xyz\oihana\schema\enumerations
 * @author  Marc Alcaraz (ekameleon)
 *
 * @see \xyz\oihana\schema\business\BusinessIdentity
 */
class BusinessIdentityRole extends Enumeration
{
    /**
     * The account is, or directly represents, a customer organization.
     */
    public const string CUSTOMER = 'customer' ;

    /**
     * The account is a contact / employee acting on behalf of a customer.
     */
    public const string CUSTOMER_CONTACT = 'customerContact' ;

    /**
     * The account is a deliverer / carrier.
     */
    public const string DELIVERER = 'deliverer' ;

    /**
     * The account is a provider / supplier.
     */
    public const string PROVIDER = 'provider' ;

    /**
     * The account is a seller / sales representative.
     */
    public const string SELLER = 'seller' ;
}
