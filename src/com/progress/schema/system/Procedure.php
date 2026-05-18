<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSPROCEDURES` system catalog table.
 *
 * `SYSPROCEDURES` contains one row for each stored procedure defined in the
 * database. Parameter metadata and source code are stored in the companion
 * tables `SYSPROCCOLUMNS` and `SYSPROCTEXT` respectively; for convenience
 * this class exposes the procedure text directly through the `procedureText`
 * property.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSPROCEDURES.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Procedure extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Identifier of the SQL user that created the procedure (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Number of input/output parameters declared by the procedure (`numargs`).
     *
     * @var int|null
     */
    public ?int $numberOfArguments ;

    /**
     * Stable identifier assigned to the procedure (`procid`).
     *
     * @var int|null
     */
    public ?int $procedureId ;

    /**
     * Owner (SQL schema) of the procedure (`procowner`).
     *
     * @var string|null
     */
    public ?string $procedureOwner ;

    /**
     * SQL source text of the procedure (`text`, materialized from
     * `SYSPROCTEXT`).
     *
     * @var string|null
     */
    public ?string $procedureText ;

    /**
     * Free-form remarks attached to the procedure (`remarks`).
     *
     * @var string|null
     */
    public ?string $remarks ;

    /**
     * Return type of the procedure, when applicable (`rettype`).
     *
     * @var string|null
     */
    public ?string $returnType ;
}
