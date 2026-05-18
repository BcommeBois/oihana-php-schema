<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSINDEXES` system
 * catalog table.
 *
 * `SYSINDEXES` contains one row for each (index, indexed-column) pair. The
 * `idxseq` column gives the ordinal position of the column inside the index.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSINDEXES.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Index
{
    /**
     * Whether the index entry is stored in abbreviated form (`abbreviate`).
     */
    public const string ABBREVIATE = 'abbreviate' ;

    /**
     * Sort direction of the indexed column: ascending or descending (`asc_desc`).
     */
    public const string ASC_DESC = 'ascDesc' ;

    /**
     * Indexed column name (`fldname` or `col` depending on revision).
     */
    public const string COLUMN = 'column' ;

    /**
     * Ordinal field number within the index definition (`fld_no`).
     */
    public const string FIELD_NUMBER = 'fieldNumber' ;

    /**
     * Owner (SQL schema) of the index itself (`idxowner`).
     */
    public const string INDEX_OWNER = 'indexOwner' ;

    /**
     * Sequence (rank) of the column inside the index (`idxseq`).
     */
    public const string INDEX_SEQUENCE = 'indexSequence' ;

    /**
     * Index type discriminator (`idxtype`): `U` for unique, `D` for duplicates.
     */
    public const string INDEX_TYPE = 'indexType' ;

    /**
     * Total number of indexed components (`numcomp`).
     */
    public const string NUMBER_OF_COMPONENTS = 'numberOfComponents' ;

    /**
     * Whether this index is the primary index of the table.
     */
    public const string PRIMARY = 'primary' ;

    /**
     * Owning table of the index.
     */
    public const string TABLE = 'table' ;

    /**
     * Whether the index enforces uniqueness (`unique`).
     */
    public const string UNIQUE = 'unique' ;
}
