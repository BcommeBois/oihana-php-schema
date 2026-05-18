<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSDATATYPES`
 * system catalog table.
 *
 * `SYSDATATYPES` lists every SQL data type that the OpenEdge SQL engine
 * supports for column definitions.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSDATATYPES.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait DataType
{
    /**
     * Maximum storage length of the data type, in bytes (`col_len`).
     */
    public const string COLUMN_LENGTH = 'columnLength' ;

    /**
     * Default numeric precision of the data type (`precision`).
     */
    public const string DATA_TYPE_PRECISION = 'dataTypePrecision' ;

    /**
     * Numeric radix used by `dataTypePrecision` (`radix`).
     */
    public const string DATA_TYPE_RADIX = 'dataTypeRadix' ;

    /**
     * Numeric SQL type code identifying the data type (`typecode`).
     */
    public const string TYPE_CODE = 'typeCode' ;

    /**
     * Canonical SQL name of the data type (`typename`).
     */
    public const string TYPE_NAME = 'typeName' ;
}
