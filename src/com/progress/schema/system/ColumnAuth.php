<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSCOLAUTH` system catalog table.
 *
 * `SYSCOLAUTH` contains one row per (grantee, column) pair and exposes the
 * column-level privileges granted to the user: `SELECT`, `UPDATE` and
 * `REFERENCES`.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSCOLAUTH.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class ColumnAuth extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Granted column (`col`).
     *
     * @var string|Column|null
     */
    public string|Column|null $column ;

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
     * Owning table of the column (`tbl`).
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
