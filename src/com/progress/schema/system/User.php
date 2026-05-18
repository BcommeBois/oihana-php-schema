<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSDBAUTH` system catalog table.
 *
 * `SYSDBAUTH` contains one row for every SQL user that has been granted
 * database-wide privileges (`DBA` and/or `RESOURCE`). It is therefore the
 * de facto list of OpenEdge SQL users, populated by `GRANT` statements such
 * as `GRANT DBA, RESOURCE TO "alice"`.
 *
 * The inherited `name` property holds the SQL identifier of the user
 * (`grantee`).
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSDBAUTH.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class User extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Whether the user has DBA privilege (`dba_acc`).
     *
     * Users with DBA privilege automatically inherit every privilege on
     * existing and future database objects.
     *
     * @var bool|null
     */
    public ?bool $dbaAccess ;

    /**
     * SQL identifier of the user receiving the privileges (`grantee`).
     *
     * @var string|null
     */
    public ?string $grantee ;

    /**
     * SQL identifier of the user that granted the privileges (`grantor`).
     *
     * @var string|null
     */
    public ?string $grantor ;

    /**
     * Whether the user has RESOURCE privilege (`res_acc`).
     *
     * Users with RESOURCE privilege can create new database objects (tables,
     * views, …) within their own schema.
     *
     * @var bool|null
     */
    public ?bool $resourceAccess ;
}
