<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\PaymentScheduleTrait;

/**
 * A multi-installment payment plan attached to a {@see BusinessDocument}
 * (e.g. "30% on order, 70% on delivery").
 *
 * Each {@see PaymentInstallment} carries a due date, an amount or percentage
 * and its own {@see PaymentInstallment::$paymentStatus}, so the plan can be
 * tracked installment by installment. Reminders are a later, more advanced
 * iteration.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class PaymentSchedule extends StructuredValue
{
    use PaymentScheduleTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The ordered list of scheduled payments.
     * @var null|array|PaymentInstallment
     */
    #[HydrateWith(PaymentInstallment::class)]
    public null|array|PaymentInstallment $installments ;
}
