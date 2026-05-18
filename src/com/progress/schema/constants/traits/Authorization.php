<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL authorization
 * system catalog tables.
 *
 * Two tables share most of their schema:
 *
 * - `SYSTABAUTH` — privileges granted on a table (`SELECT`, `INSERT`,
 *   `UPDATE`, `DELETE`, `REFERENCES`, `INDEX`, `ALTER`)
 * - `SYSCOLAUTH` — privileges granted on individual columns (`SELECT`,
 *   `UPDATE`, `REFERENCES`)
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTABAUTH.html
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSCOLAUTH.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Authorization
{
    /**
     * `ALTER` privilege flag (table-level).
     */
    public const string ALTER = 'alter' ;

    /**
     * `DELETE` privilege flag (table-level).
     */
    public const string DELETE = 'delete' ;

    /**
     * `INDEX` privilege flag (table-level).
     */
    public const string INDEX = 'index' ;

    /**
     * `INSERT` privilege flag (table-level).
     */
    public const string INSERT = 'insert' ;

    /**
     * `REFERENCES` privilege flag.
     */
    public const string REFERENCES = 'references' ;

    /**
     * `SELECT` privilege flag.
     */
    public const string SELECT = 'select' ;

    /**
     * `UPDATE` privilege flag.
     */
    public const string UPDATE = 'update' ;
}
