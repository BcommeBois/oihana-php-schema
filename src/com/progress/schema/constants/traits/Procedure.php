<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSPROCEDURES`
 * system catalog table.
 *
 * `SYSPROCEDURES` contains one row for each stored procedure defined in the
 * database. Procedure parameter metadata and the source text are stored in
 * the companion tables `SYSPROCCOLUMNS` and `SYSPROCTEXT`.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSPROCEDURES.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Procedure
{
    /**
     * Number of input/output parameters declared by the procedure (`numargs`).
     */
    public const string NUMBER_OF_ARGUMENTS = 'numberOfArguments' ;

    /**
     * Stable identifier assigned to the procedure (`procid`).
     */
    public const string PROCEDURE_ID = 'procedureId' ;

    /**
     * Owner (SQL schema) of the procedure (`procowner`).
     */
    public const string PROCEDURE_OWNER = 'procedureOwner' ;

    /**
     * SQL source text of the procedure (`text` / `SYSPROCTEXT`).
     */
    public const string PROCEDURE_TEXT = 'procedureText' ;

    /**
     * Free-form remarks attached to the procedure (`remarks`).
     */
    public const string REMARKS = 'remarks' ;

    /**
     * Return type of the procedure, when applicable (`rettype`).
     */
    public const string RETURN_TYPE = 'returnType' ;
}
