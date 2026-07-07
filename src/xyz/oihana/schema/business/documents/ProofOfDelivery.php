<?php

namespace xyz\oihana\schema\business\documents;

use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\ProofOfDeliveryTrait;

/**
 * The confirmation that the goods of a {@see DeliveryNote} were received —
 * who signed for them, when, and any discrepancy noted at that moment
 * between what was delivered and what the recipient actually confirmed.
 *
 * A pure trace, not an engine, in the same spirit as {@see PaymentReminder} :
 * it records what was captured at the point of delivery, not a signature
 * capture mechanism or a dispute-resolution workflow, which stay consumer
 * concerns.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class ProofOfDelivery extends StructuredValue
{
    use ProofOfDeliveryTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The date the delivery was confirmed.
     * @var string|int|null
     */
    public null|string|int $date ;

    /**
     * Any discrepancy noted between what was delivered and what was confirmed received.
     * @var string|null
     */
    public ?string $discrepancyNote ;

    /**
     * The name of the person who signed for the delivery.
     * @var string|null
     */
    public ?string $signatory ;
}
