<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\DeliveryLine} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait DeliveryLineTrait
{
    const string BACKORDER_QUANTITY = 'backorderQuantity' ;
    const string BACKORDER_REASON   = 'backorderReason' ;
    const string BATCH_NUMBER       = 'batchNumber' ;
    const string DELIVERED_QUANTITY = 'deliveredQuantity' ;
    const string ITEM               = 'item' ;
    const string ORDERED_QUANTITY   = 'orderedQuantity' ;
    const string POSITION           = 'position' ;
    const string SERIAL_NUMBERS     = 'serialNumbers' ;
}
