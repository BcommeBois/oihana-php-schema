<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYS_KEYCOL_USAGE` system catalog
 * table.
 *
 * `SYS_KEYCOL_USAGE` lists the columns that participate in a `PRIMARY KEY`,
 * `UNIQUE` or `FOREIGN KEY` constraint. Multi-column keys are represented
 * by several rows whose `keySequence` (`keyseq`) gives the ordinal position
 * of the column inside the key.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/Overview-of-system-catalog-tables.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class KeyColumnUsage extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Column participating in the key (`col`).
     *
     * @var string|Column|null
     */
    public string|Column|null $column ;

    /**
     * Name of the constraint that this column participates in
     * (`constrname`).
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

    /**
     * Position of the column within a multi-column key (`keyseq`),
     * starting at 1.
     *
     * @var int|null
     */
    public ?int $keySequence ;

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
}
