<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSSEQUENCES`
 * system catalog table.
 *
 * `SYSSEQUENCES` contains one row for each sequence generator defined in the
 * database.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSSEQUENCES.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Sequence
{
    /**
     * Whether the sequence cycles back to its minimum once `maxValue` is
     * reached (`cycle`).
     */
    public const string CYCLE = 'cycle' ;

    /**
     * Increment between two successive values (`increment`).
     */
    public const string INCREMENT = 'increment' ;

    /**
     * Initial value of the sequence (`initial`).
     */
    public const string INITIAL_VALUE = 'initialValue' ;

    /**
     * Maximum value of the sequence (`maxval`).
     */
    public const string MAX_VALUE = 'maxValue' ;

    /**
     * Minimum value of the sequence (`minval`).
     */
    public const string MIN_VALUE = 'minValue' ;

    /**
     * Owner (SQL schema) of the sequence (`seqowner`).
     */
    public const string SEQUENCE_OWNER = 'sequenceOwner' ;
}
