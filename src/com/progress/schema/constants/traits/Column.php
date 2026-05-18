<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSCOLUMNS` system
 * catalog table.
 *
 * `SYSCOLUMNS` contains one row for every column of every table and view in
 * the database, including system tables.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSCOLUMNS.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Column
{
    /**
     * Character set used by the column (`charset`).
     */
    public const string CHAR_SET = 'charSet' ;

    /**
     * Whether the column comparison is case sensitive (`case-sensitive`).
     */
    public const string CASE_SENSITIVE = 'caseSensitive' ;

    /**
     * Ordinal position of the column within the table (`colid`).
     */
    public const string COLUMN_ID = 'columnId' ;

    /**
     * Data type of the column (`coltype`), e.g. `INTEGER`, `VARCHAR`.
     */
    public const string COLUMN_TYPE = 'columnType' ;

    /**
     * Collation used by the column (`collation`).
     */
    public const string COLLATION = 'collation' ;

    /**
     * Number of decimal digits after the decimal point (`decimal`).
     */
    public const string DECIMAL = 'decimal' ;

    /**
     * Default value of the column (`dflt_value`).
     */
    public const string DEFAULT_VALUE = 'defaultValue' ;

    /**
     * Display format string used by the OpenEdge 4GL (`format`).
     */
    public const string FORMAT = 'format' ;

    /**
     * Display label used by the OpenEdge 4GL (`label`).
     */
    public const string LABEL = 'label' ;

    /**
     * Whether the column is mandatory in the 4GL schema (`mandatory`).
     */
    public const string MANDATORY = 'mandatory' ;

    /**
     * Whether the column accepts the SQL NULL value (`nulls`/`nullFlag`).
     */
    public const string NULL_FLAG = 'nullFlag' ;

    /**
     * Numeric precision of the column (`precision`).
     */
    public const string PRECISION = 'precision' ;

    /**
     * Numeric radix used by `precision` (`radix`).
     */
    public const string RADIX = 'radix' ;

    /**
     * Numeric scale of the column (`scale`).
     */
    public const string SCALE = 'scale' ;

    /**
     * Owning table of the column.
     */
    public const string TABLE = 'table' ;

    /**
     * Storage width of the column in bytes (`width`).
     */
    public const string WIDTH = 'width' ;
}
