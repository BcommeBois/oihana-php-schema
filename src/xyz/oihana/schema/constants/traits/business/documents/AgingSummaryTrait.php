<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\AgingSummary} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait AgingSummaryTrait
{
    const string CURRENT       = 'current' ;
    const string DAYS_1_TO_30  = 'days1To30' ;
    const string DAYS_31_TO_60 = 'days31To60' ;
    const string DAYS_61_TO_90 = 'days61To90' ;
    const string OVER_90       = 'over90' ;
}
