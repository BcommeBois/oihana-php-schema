<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys shared by several OpenEdge SQL system catalog tables.
 *
 * These constants centralize names that appear with the same semantics in
 * multiple SYS% tables, such as the owner of a database object, the creator,
 * the creation date or a generic description.
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Common
{
    /**
     * Creation timestamp of the catalog entry (`crdate`).
     */
    public const string CREATION_DATE = 'creationDate' ;

    /**
     * Identifier of the SQL user that created the entry (`creator`).
     */
    public const string CREATOR = 'creator' ;

    /**
     * Free-form description of the catalog entry (`description`/`remarks`).
     */
    public const string DESCRIPTION = 'description' ;

    /**
     * Internal numeric identifier assigned by OpenEdge to the object.
     */
    public const string ID = 'id' ;

    /**
     * Name of the catalog entry (table, column, index, view, sequence, …).
     */
    public const string NAME = 'name' ;

    /**
     * Owner (SQL schema) of the catalog entry (`owner`/`tblowner`).
     */
    public const string OWNER = 'owner' ;

    /**
     * Lifecycle status flag of the catalog entry (`status`).
     */
    public const string STATUS = 'status' ;

    /**
     * Owning table reference (`tbl`/`tblname`).
     */
    public const string TABLE = 'table' ;

    /**
     * Owner of the referenced table (`tblowner`).
     */
    public const string TABLE_OWNER = 'tableOwner' ;

    /**
     * Type discriminator of the catalog entry (e.g. `T`, `V`, `S` for tables).
     */
    public const string TYPE = 'type' ;
}
