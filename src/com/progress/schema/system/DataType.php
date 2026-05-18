<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSDATATYPES` system catalog table.
 *
 * `SYSDATATYPES` lists every SQL data type that the OpenEdge SQL engine
 * supports for column definitions.
 *
 * The inherited `name` property holds the canonical SQL name of the data
 * type (`typename`).
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSDATATYPES.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class DataType extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Maximum storage length of the data type, in bytes (`col_len`).
     *
     * @var int|null
     */
    public ?int $columnLength ;

    /**
     * Default numeric precision of the data type (`precision`).
     *
     * @var int|null
     */
    public ?int $dataTypePrecision ;

    /**
     * Numeric radix used by `dataTypePrecision` (`radix`), typically 2 or 10.
     *
     * @var int|null
     */
    public ?int $dataTypeRadix ;

    /**
     * Numeric SQL type code identifying the data type (`typecode`).
     *
     * @var int|null
     */
    public ?int $typeCode ;
}
