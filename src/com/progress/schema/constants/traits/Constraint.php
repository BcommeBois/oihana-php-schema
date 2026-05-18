<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys shared by the constraint-related OpenEdge SQL system catalog
 * tables.
 *
 * The constraint metadata is split across several tables in OpenEdge SQL:
 *
 * - `SYS_TBL_CONSTRS`   — table-level constraint header
 * - `SYS_CHK_CONSTRS`   — `CHECK` constraint text
 * - `SYS_REF_CONSTRS`   — referential (foreign key) constraint rules
 * - `SYS_KEYCOL_USAGE` — columns participating in a key constraint
 * - `SYS_CHKCOL_USAGE` — columns referenced by a `CHECK` constraint
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/Overview-of-system-catalog-tables.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Constraint
{
    /**
     * Column name participating in the constraint
     * (`SYS_KEYCOL_USAGE.col` / `SYS_CHKCOL_USAGE.col`).
     */
    public const string COLUMN = 'column' ;

    /**
     * Name of the constraint (`constrname`).
     */
    public const string CONSTRAINT_NAME = 'constraintName' ;

    /**
     * Owner (SQL schema) of the constraint (`constrowner`).
     */
    public const string CONSTRAINT_OWNER = 'constraintOwner' ;

    /**
     * Constraint type (`constrtype`): `P` (primary key), `U` (unique),
     * `F` (foreign key) or `C` (check).
     */
    public const string CONSTRAINT_TYPE = 'constraintType' ;

    /**
     * Deferrability mode of the constraint (`deferrability`).
     */
    public const string DEFERRABILITY = 'deferrability' ;

    /**
     * `DELETE` referential action (`delete_rule`): `C` (cascade), `N` (set
     * null), `D` (set default), `R` (restrict) or `A` (no action).
     */
    public const string DELETE_RULE = 'deleteRule' ;

    /**
     * Position of the column within a multi-column key (`keyseq`).
     */
    public const string KEY_SEQUENCE = 'keySequence' ;

    /**
     * `MATCH` mode of a referential constraint (`match_type`).
     */
    public const string MATCH_TYPE = 'matchType' ;

    /**
     * SQL expression of a `CHECK` constraint
     * (`SYS_CHK_CONSTRS.chktext`).
     */
    public const string CHECK_TEXT = 'checkText' ;

    /**
     * Identifier of the unique/primary key constraint referenced by a
     * foreign key (`uniquename`).
     */
    public const string UNIQUE_NAME = 'uniqueName' ;

    /**
     * Owner (SQL schema) of the referenced unique/primary key constraint
     * (`uniqueowner`).
     */
    public const string UNIQUE_OWNER = 'uniqueOwner' ;

    /**
     * `UPDATE` referential action (`update_rule`): `C` (cascade), `N` (set
     * null), `D` (set default), `R` (restrict) or `A` (no action).
     */
    public const string UPDATE_RULE = 'updateRule' ;
}
