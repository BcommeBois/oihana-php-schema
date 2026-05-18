<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSCOLUMNS` system catalog table.
 *
 * `SYSCOLUMNS` contains one row for each column of every table and view in
 * the database, including system tables.
 *
 * Inherited identification fields from `Thing`:
 *
 * - `id`   maps to the underlying numeric column identifier (`colid`)
 * - `name` maps to the column name (`col`)
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSCOLUMNS.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Column extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Whether the column comparison is case sensitive (`case-sensitive`).
     *
     * @var bool|null
     */
    public ?bool $caseSensitive ;

    /**
     * Character set used by the column (`charset`).
     *
     * @var string|null
     */
    public ?string $charSet ;

    /**
     * Collation used by the column (`collation`).
     *
     * @var string|null
     */
    public ?string $collation ;

    /**
     * Ordinal position of the column within the table (`colid`),
     * starting at 1.
     *
     * @var int|null
     */
    public ?int $columnId ;

    /**
     * SQL data type of the column (`coltype`), e.g. `INTEGER`, `VARCHAR`,
     * `TIMESTAMP`.
     *
     * @var string|null
     */
    public ?string $columnType ;

    /**
     * Number of decimal digits after the decimal point (`decimal`).
     *
     * @var int|null
     */
    public ?int $decimal ;

    /**
     * Default value of the column (`dflt_value`), serialized as a string.
     *
     * @var string|null
     */
    public ?string $defaultValue ;

    /**
     * Display format string used by the OpenEdge 4GL (`format`).
     *
     * @var string|null
     */
    public ?string $format ;

    /**
     * Display label used by the OpenEdge 4GL (`label`).
     *
     * @var string|null
     */
    public ?string $label ;

    /**
     * Whether the column is mandatory in the OpenEdge 4GL schema
     * (`mandatory`).
     *
     * @var bool|null
     */
    public ?bool $mandatory ;

    /**
     * Whether the column accepts the SQL NULL value (`nulls`/`nullFlag`).
     *
     * @var bool|null
     */
    public ?bool $nullFlag ;

    /**
     * Numeric precision of the column (`precision`).
     *
     * @var int|null
     */
    public ?int $precision ;

    /**
     * Numeric radix used by `precision` (`radix`), typically 2 or 10.
     *
     * @var int|null
     */
    public ?int $radix ;

    /**
     * Numeric scale of the column (`scale`).
     *
     * @var int|null
     */
    public ?int $scale ;

    /**
     * Owning table of the column (`tbl`).
     *
     * @var string|Table|null
     */
    public string|Table|null $table ;

    /**
     * Storage width of the column in bytes (`width`).
     *
     * @var int|null
     */
    public ?int $width ;
}
