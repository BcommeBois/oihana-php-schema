<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYS_REF_CONSTRS` system catalog table.
 *
 * `SYS_REF_CONSTRS` lists the referential (foreign key) constraints declared
 * in the database, together with their `MATCH`, `UPDATE` and `DELETE` rules
 * and a reference to the unique/primary key constraint they target.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/Overview-of-system-catalog-tables.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class ReferentialConstraint extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Name of the referential constraint (`constrname`).
     *
     * @var string|null
     */
    public ?string $constraintName ;

    /**
     * Owner (SQL schema) of the referential constraint (`constrowner`).
     *
     * @var string|null
     */
    public ?string $constraintOwner ;

    /**
     * `DELETE` referential action (`delete_rule`):
     *
     * - `C` — cascade
     * - `N` — set null
     * - `D` — set default
     * - `R` — restrict
     * - `A` — no action
     *
     * @var string|null
     */
    public ?string $deleteRule ;

    /**
     * `MATCH` mode of the referential constraint (`match_type`).
     *
     * @var string|null
     */
    public ?string $matchType ;

    /**
     * Name of the unique/primary key constraint referenced by this foreign
     * key (`uniquename`).
     *
     * @var string|null
     */
    public ?string $uniqueName ;

    /**
     * Owner (SQL schema) of the referenced unique/primary key constraint
     * (`uniqueowner`).
     *
     * @var string|null
     */
    public ?string $uniqueOwner ;

    /**
     * `UPDATE` referential action (`update_rule`):
     *
     * - `C` — cascade
     * - `N` — set null
     * - `D` — set default
     * - `R` — restrict
     * - `A` — no action
     *
     * @var string|null
     */
    public ?string $updateRule ;
}
