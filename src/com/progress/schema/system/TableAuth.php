<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSTABAUTH` system catalog table.
 *
 * `SYSTABAUTH` contains one row per (grantee, table) pair and exposes the
 * table-level privileges granted to the user: `SELECT`, `INSERT`, `UPDATE`,
 * `DELETE`, `REFERENCES`, `INDEX` and `ALTER`.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTABAUTH.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class TableAuth extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * `ALTER` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $alter ;

    /**
     * `DELETE` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $delete ;

    /**
     * SQL identifier of the user receiving the privileges (`grantee`).
     *
     * @var string|null
     */
    public ?string $grantee ;

    /**
     * SQL identifier of the user that granted the privileges (`grantor`).
     *
     * @var string|null
     */
    public ?string $grantor ;

    /**
     * `INDEX` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $index ;

    /**
     * `INSERT` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $insert ;

    /**
     * `REFERENCES` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $references ;

    /**
     * `SELECT` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $select ;

    /**
     * Owning table of the granted privilege (`tbl`).
     *
     * @var string|Table|null
     */
    public string|Table|null $table ;

    /**
     * Owner (SQL schema) of the owning table (`tblowner`).
     *
     * @var string|null
     */
    public ?string $tableOwner ;

    /**
     * `UPDATE` privilege flag.
     *
     * @var bool|null
     */
    public ?bool $update ;
}
