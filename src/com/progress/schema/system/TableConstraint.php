<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYS_TBL_CONSTRS` system catalog table.
 *
 * `SYS_TBL_CONSTRS` is the header table that lists every table-level
 * constraint (`PRIMARY KEY`, `UNIQUE`, `FOREIGN KEY`, `CHECK`). Companion
 * tables provide additional detail:
 *
 * - `SYS_CHK_CONSTRS`   — `CHECK` constraint text
 * - `SYS_REF_CONSTRS`   — referential constraint rules
 * - `SYS_KEYCOL_USAGE` — columns participating in the key
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/Overview-of-system-catalog-tables.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class TableConstraint extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Constraint type (`constrtype`):
     *
     * - `P` — primary key
     * - `U` — unique
     * - `F` — foreign key
     * - `C` — check
     *
     * @var string|null
     */
    public ?string $constraintType ;

    /**
     * Owner (SQL schema) of the constraint (`constrowner`).
     *
     * @var string|null
     */
    public ?string $constraintOwner ;

    /**
     * Deferrability mode of the constraint (`deferrability`).
     *
     * @var string|null
     */
    public ?string $deferrability ;

    /**
     * Lifecycle status of the constraint (`status`).
     *
     * @var string|null
     */
    public ?string $status ;

    /**
     * Owning table of the constraint (`tbl`).
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
}
