<?php

namespace xyz\oihana\schema\products;

use org\schema\Organization;
use org\schema\PaymentMethod as SchemaPaymentMethod ;

use xyz\oihana\schema\constants\Oihana;

/**
 * A payment method is a standardized procedure for transferring the monetary amount for a purchase.
 *
 * Payment methods are characterized by the legal and technical structures used,
 * and by the organization or group carrying out the transaction.
 *
 * @see https://schema.org/PaymentMethod
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class PaymentMethod extends SchemaPaymentMethod
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The payment method is accepted by the specific company.
     * @var array|string|Organization|null
     */
    public null|array|string|Organization $acceptedBy ;

    /**
     * Indicates if the payment method is the primary accepted.
     * @var bool
     */
    public bool $primary ;
}