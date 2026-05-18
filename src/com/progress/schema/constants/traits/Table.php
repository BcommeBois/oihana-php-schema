<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSTABLES` system
 * catalog table.
 *
 * `SYSTABLES` contains one row for every table, view and system table that is
 * defined in the database.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTABLES.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Table
{
    /**
     * Collection of columns owned by this table (denormalized convenience key).
     */
    public const string COLUMNS = 'columns' ;

    /**
     * Number of rows in the table at the last statistics update (`numrows`).
     */
    public const string NUMBER_OF_ROWS = 'numberOfRows' ;

    /**
     * Percentage of the table that has been touched since the last
     * statistics update (`pcttouched`).
     */
    public const string PERCENT_TOUCHED = 'percentTouched' ;

    /**
     * Average record size in bytes (`recsize`).
     */
    public const string RECORD_SIZE = 'recordSize' ;

    /**
     * Bitmask of table attributes (`tblattributes`).
     */
    public const string TABLE_ATTRIBUTES = 'tableAttributes' ;

    /**
     * Timestamp of the last automatic statistics update (`updstats`).
     */
    public const string UPDATE_STATS = 'updateStats' ;
}
