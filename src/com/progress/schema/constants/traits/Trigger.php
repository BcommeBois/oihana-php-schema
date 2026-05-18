<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSTRIGGER`
 * system catalog table.
 *
 * `SYSTRIGGER` contains one row for each trigger defined on a table.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTRIGGER.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Trigger
{
    /**
     * Trigger event: `I` (INSERT), `U` (UPDATE) or `D` (DELETE) (`event`).
     */
    public const string EVENT = 'event' ;

    /**
     * Granularity flag: `R` (FOR EACH ROW) or `S` (FOR EACH STATEMENT)
     * (`for_each`).
     */
    public const string FOR_EACH = 'forEach' ;

    /**
     * SQL source text of the trigger body (`trigtext`).
     */
    public const string TRIGGER_TEXT = 'triggerText' ;

    /**
     * Owner (SQL schema) of the trigger (`trigowner`).
     */
    public const string TRIGGER_OWNER = 'triggerOwner' ;

    /**
     * Trigger timing: `B` (BEFORE) or `A` (AFTER) (`when`).
     */
    public const string TIMING = 'timing' ;
}
