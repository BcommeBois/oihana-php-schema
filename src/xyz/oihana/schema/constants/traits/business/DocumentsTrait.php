<?php

namespace xyz\oihana\schema\constants\traits\business;

use xyz\oihana\schema\constants\traits\business\documents\AdjustmentTrait;
use xyz\oihana\schema\constants\traits\business\documents\AppliedEcoFeeTrait;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentLineTrait;
use xyz\oihana\schema\constants\traits\business\documents\DocumentTotalsTrait;
use xyz\oihana\schema\constants\traits\business\documents\EcoFeeRuleTrait;
use xyz\oihana\schema\constants\traits\business\documents\PaymentInstallmentTrait;
use xyz\oihana\schema\constants\traits\business\documents\PaymentScheduleTrait;
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
    use AdjustmentTrait          ,
        AppliedEcoFeeTrait       ,
        BusinessDocumentLineTrait ,
        DocumentTotalsTrait      ,
        EcoFeeRuleTrait          ,
        PaymentInstallmentTrait  ,
        PaymentScheduleTrait     ,
        TaxDetailTrait           ;
}
