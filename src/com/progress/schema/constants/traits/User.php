<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSDBAUTH` system
 * catalog table.
 *
 * `SYSDBAUTH` contains one row for every SQL user that has been granted
 * database-wide privileges (DBA and/or RESOURCE). It is therefore the de facto
 * list of OpenEdge SQL users.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSDBAUTH.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait User
{
    /**
     * Whether the user has DBA privilege (`dba_acc`).
     */
    public const string DBA_ACCESS = 'dbaAccess' ;

    /**
     * Identifier of the user that granted the privileges (`grantor`).
     */
    public const string GRANTOR = 'grantor' ;

    /**
     * SQL user name receiving the privileges (`grantee`).
     */
    public const string GRANTEE = 'grantee' ;

    /**
     * Whether the user has RESOURCE privilege (`res_acc`).
     */
    public const string RESOURCE_ACCESS = 'resourceAccess' ;
}
