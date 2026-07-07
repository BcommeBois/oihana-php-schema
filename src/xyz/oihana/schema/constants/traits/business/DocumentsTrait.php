<?php

namespace xyz\oihana\schema\constants\traits\business;

use xyz\oihana\schema\constants\traits\business\documents\AdjustmentTrait;
use xyz\oihana\schema\constants\traits\business\documents\AgingSummaryTrait;
use xyz\oihana\schema\constants\traits\business\documents\AppliedEcoFeeTrait;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentLineTrait;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentTrait;
use xyz\oihana\schema\constants\traits\business\documents\CreditNoteTrait;
use xyz\oihana\schema\constants\traits\business\documents\DeliveryLineTrait;
use xyz\oihana\schema\constants\traits\business\documents\DeliveryNoteTrait;
use xyz\oihana\schema\constants\traits\business\documents\DocumentTotalsTrait;
use xyz\oihana\schema\constants\traits\business\documents\EcoFeeRuleTrait;
use xyz\oihana\schema\constants\traits\business\documents\InvoiceTrait;
use xyz\oihana\schema\constants\traits\business\documents\PaymentInstallmentTrait;
use xyz\oihana\schema\constants\traits\business\documents\PaymentReminderTrait;
use xyz\oihana\schema\constants\traits\business\documents\PaymentScheduleTrait;
use xyz\oihana\schema\constants\traits\business\documents\ProofOfDeliveryTrait;
use xyz\oihana\schema\constants\traits\business\documents\PurchaseOrderTrait;
use xyz\oihana\schema\constants\traits\business\documents\QuoteTrait;
use xyz\oihana\schema\constants\traits\business\documents\ReceiptTrait;
use xyz\oihana\schema\constants\traits\business\documents\StatementEntryTrait;
use xyz\oihana\schema\constants\traits\business\documents\StatementTrait;
use xyz\oihana\schema\constants\traits\business\documents\TaxDetailTrait;

/**
 * The enumeration of all business-document properties.
 *
 * @package xyz\oihana\schema\constants\traits\business
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait DocumentsTrait
{
    use AdjustmentTrait           ,
        AgingSummaryTrait         ,
        AppliedEcoFeeTrait        ,
        BusinessDocumentLineTrait ,
        BusinessDocumentTrait     ,
        CreditNoteTrait           ,
        DeliveryLineTrait         ,
        DeliveryNoteTrait         ,
        DocumentTotalsTrait       ,
        EcoFeeRuleTrait           ,
        InvoiceTrait              ,
        PaymentInstallmentTrait   ,
        PaymentReminderTrait      ,
        PaymentScheduleTrait      ,
        ProofOfDeliveryTrait      ,
        PurchaseOrderTrait        ,
        QuoteTrait                ,
        ReceiptTrait              ,
        StatementEntryTrait       ,
        StatementTrait            ,
        TaxDetailTrait            ;
}
