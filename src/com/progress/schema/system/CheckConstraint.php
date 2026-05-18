<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYS_CHK_CONSTRS` system catalog table.
 *
 * `SYS_CHK_CONSTRS` contains the SQL expression of each `CHECK` constraint
 * declared in the database. It is joined to `SYS_TBL_CONSTRS` on the
 * `constraintName` / `constraintOwner` pair.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/Overview-of-system-catalog-tables.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class CheckConstraint extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * SQL expression of the `CHECK` constraint (`chktext`).
     *
     * @var string|null
     */
    public ?string $checkText ;

    /**
     * Name of the constraint (`constrname`).
     *
     * @var string|null
     */
    public ?string $constraintName ;

    /**
     * Owner (SQL schema) of the constraint (`constrowner`).
     *
     * @var string|null
     */
    public ?string $constraintOwner ;
}
