<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSINDEXES` system catalog table.
 *
 * `SYSINDEXES` contains one row for each (index, indexed-column) pair.
 * Multi-column indexes are therefore represented by several rows whose
 * `indexSequence` (`idxseq`) column gives the ordinal position of the
 * indexed component, starting at 0.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSINDEXES.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Index extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Whether index entries are stored in abbreviated form (`abbreviate`).
     *
     * @var bool|null
     */
    public ?bool $abbreviate ;

    /**
     * Sort direction of the indexed column (`asc_desc`):
     *
     * - `A` — ascending
     * - `D` — descending
     *
     * @var string|null
     */
    public ?string $ascDesc ;

    /**
     * Indexed column name (`fldname`/`col`).
     *
     * @var string|Column|null
     */
    public string|Column|null $column ;

    /**
     * Identifier of the SQL user that created the index (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Ordinal field number within the index definition (`fld_no`).
     *
     * @var int|null
     */
    public ?int $fieldNumber ;

    /**
     * Owner (SQL schema) of the index itself (`idxowner`).
     *
     * @var string|null
     */
    public ?string $indexOwner ;

    /**
     * Sequence (rank) of the column inside the index (`idxseq`),
     * starting at 0.
     *
     * @var int|null
     */
    public ?int $indexSequence ;

    /**
     * Index type discriminator (`idxtype`):
     *
     * - `U` — unique
     * - `D` — duplicates allowed
     *
     * @var string|null
     */
    public ?string $indexType ;

    /**
     * Total number of indexed components (`numcomp`).
     *
     * @var int|null
     */
    public ?int $numberOfComponents ;

    /**
     * Whether this index is the primary index of the table.
     *
     * @var bool|null
     */
    public ?bool $primary ;

    /**
     * Owning table of the index (`tbl`).
     *
     * @var string|Table|null
     */
    public string|Table|null $table ;

    /**
     * Whether the index enforces uniqueness (`unique`).
     *
     * @var bool|null
     */
    public ?bool $unique ;
}
