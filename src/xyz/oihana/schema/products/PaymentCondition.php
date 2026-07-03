<?php

namespace xyz\oihana\schema\products;

use org\schema\Organization;
use org\schema\PaymentMethod as SchemaPaymentMethod ;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a payment condition applied on specific organizations.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class PaymentCondition extends SchemaPaymentMethod
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
     * Number of days after invoicing.
     * @var int|null
     */
    public null|int $paymentDueDays ;

    /**
     * Indicates if the payment method is the primary accepted.
     * @var bool
     */
    public bool $primary ;
}