<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSSYNONYMS` system catalog table.
 *
 * `SYSSYNONYMS` contains one row for each synonym defined in the database.
 * A synonym is an alternative name for an existing table or view.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSSYNONYMS.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Synonym extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Name of the table or view that the synonym refers to (`basetbl`).
     *
     * @var string|Table|View|null
     */
    public string|Table|View|null $baseTable ;

    /**
     * Owner (SQL schema) of the referenced base table (`basetblowner`).
     *
     * @var string|null
     */
    public ?string $baseTableOwner ;

    /**
     * Identifier of the SQL user that created the synonym (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Owner (SQL schema) of the synonym itself (`synowner`).
     *
     * @var string|null
     */
    public ?string $synonymOwner ;
}
