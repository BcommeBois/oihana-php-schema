<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSSYNONYMS`
 * system catalog table.
 *
 * `SYSSYNONYMS` contains one row for each synonym defined in the database.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSSYNONYMS.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Synonym
{
    /**
     * Name of the table (or view) that the synonym refers to (`basetbl`).
     */
    public const string BASE_TABLE = 'baseTable' ;

    /**
     * Owner (SQL schema) of the referenced base table (`basetblowner`).
     */
    public const string BASE_TABLE_OWNER = 'baseTableOwner' ;

    /**
     * Owner (SQL schema) of the synonym itself (`synowner`).
     */
    public const string SYNONYM_OWNER = 'synonymOwner' ;
}
