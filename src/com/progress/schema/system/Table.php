<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSTABLES` system catalog table.
 *
 * `SYSTABLES` contains one row for each table, view and system table defined
 * in the database. The owner of the system tables is always `sysprogress`.
 *
 * Inherited identification fields from `Thing`:
 *
 * - `id`   maps to the underlying `tbl` numeric identifier
 * - `name` maps to the table name (`tbl`)
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTABLES.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Table extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Convenience denormalized list of the columns belonging to this table.
     *
     * Not part of `SYSTABLES` itself: typically populated by an application
     * that joined `SYSTABLES` with `SYSCOLUMNS`.
     *
     * @var array<Column>|null
     */
    public ?array $columns ;

    /**
     * Identifier of the SQL user that created the table (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Number of rows in the table at the last statistics update (`numrows`).
     *
     * @var int|null
     */
    public ?int $numberOfRows ;

    /**
     * Percentage of the table that has been touched since the last
     * statistics update (`pcttouched`).
     *
     * @var int|null
     */
    public ?int $percentTouched ;

    /**
     * Average record size in bytes (`recsize`).
     *
     * @var int|null
     */
    public ?int $recordSize ;

    /**
     * Lifecycle status of the table (`status`).
     *
     * @var string|null
     */
    public ?string $status ;

    /**
     * Bitmask of table attributes (`tblattributes`).
     *
     * @var int|null
     */
    public ?int $tableAttributes ;

    /**
     * Type discriminator of the table (`tbltype`):
     *
     * - `T` — user table
     * - `V` — view
     * - `S` — system table
     *
     * @var string|null
     */
    public ?string $type ;

    /**
     * Timestamp of the last automatic statistics update (`updstats`).
     *
     * @var string|null
     */
    public ?string $updateStats ;
}
