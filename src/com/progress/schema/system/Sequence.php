<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSSEQUENCES` system catalog table.
 *
 * `SYSSEQUENCES` contains one row for each sequence generator defined in
 * the database.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSSEQUENCES.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Sequence extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Whether the sequence cycles back to its minimum value once `maxValue`
     * is reached (`cycle`).
     *
     * @var bool|null
     */
    public ?bool $cycle ;

    /**
     * Increment between two successive values (`increment`).
     *
     * @var int|null
     */
    public ?int $increment ;

    /**
     * Initial value of the sequence (`initial`).
     *
     * @var int|null
     */
    public ?int $initialValue ;

    /**
     * Maximum value of the sequence (`maxval`).
     *
     * @var int|null
     */
    public ?int $maxValue ;

    /**
     * Minimum value of the sequence (`minval`).
     *
     * @var int|null
     */
    public ?int $minValue ;

    /**
     * Owner (SQL schema) of the sequence (`seqowner`).
     *
     * @var string|null
     */
    public ?string $sequenceOwner ;
}
