<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSTRIGGER` system catalog table.
 *
 * `SYSTRIGGER` contains one row for each trigger defined on a table.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSTRIGGER.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Trigger extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Identifier of the SQL user that created the trigger (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Trigger event (`event`):
     *
     * - `I` — INSERT
     * - `U` — UPDATE
     * - `D` — DELETE
     *
     * @var string|null
     */
    public ?string $event ;

    /**
     * Granularity flag (`for_each`):
     *
     * - `R` — `FOR EACH ROW`
     * - `S` — `FOR EACH STATEMENT`
     *
     * @var string|null
     */
    public ?string $forEach ;

    /**
     * Owning table of the trigger (`tbl`).
     *
     * @var string|Table|null
     */
    public string|Table|null $table ;

    /**
     * Trigger timing (`when`):
     *
     * - `B` — `BEFORE`
     * - `A` — `AFTER`
     *
     * @var string|null
     */
    public ?string $timing ;

    /**
     * Owner (SQL schema) of the trigger (`trigowner`).
     *
     * @var string|null
     */
    public ?string $triggerOwner ;

    /**
     * SQL source text of the trigger body (`trigtext`).
     *
     * @var string|null
     */
    public ?string $triggerText ;
}
