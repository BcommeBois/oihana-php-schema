<?php

namespace xyz\oihana\schema\constants\traits\statistics;

/**
 * The property name constants of the {@see \xyz\oihana\schema\traits\HasReceivables} trait.
 *
 * @package xyz\oihana\schema\constants\traits\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait HasReceivablesTrait
{
    const string DAYS_LATE           = 'daysLate'          ;
    const string DOUBTFUL            = 'doubtful'          ;
    const string NUMBER_OF_DOCUMENTS = 'numberOfDocuments' ;
    const string OVERDUE             = 'overdue'           ;
    const string OVERDUE_1_TO_30     = 'overdue1To30'      ;
    const string OVERDUE_31_TO_60    = 'overdue31To60'     ;
    const string OVERDUE_61_TO_90    = 'overdue61To90'     ;
    const string OVERDUE_OVER_90     = 'overdueOver90'     ;
}
