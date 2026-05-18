<?php

namespace com\progress\schema\constants ;

use com\progress\schema\constants\traits\Properties ;

/**
 * Collection of property name constants for the Progress OpenEdge SQL system
 * catalog vocabulary.
 *
 * This class exposes, through a composition of traits, the full set of
 * property keys that map to columns of the OpenEdge SQL system catalog tables
 * (SYSTABLES, SYSCOLUMNS, SYSINDEXES, SYSVIEWS, SYSDBAUTH, SYSTABAUTH,
 * SYSCOLAUTH, SYSSEQUENCES, SYSSYNONYMS, SYSPROCEDURES, SYSTRIGGER,
 * SYS_TBL_CONSTRS, SYS_CHK_CONSTRS, SYS_REF_CONSTRS, SYS_KEYCOL_USAGE,
 * SYSDATATYPES, …).
 *
 * Using these constants instead of raw strings prevents typos, makes
 * refactoring easier and improves IDE auto-completion.
 *
 * Typical usage:
 * ```php
 * use com\progress\schema\constants\Progress ;
 * use com\progress\schema\system\Table ;
 *
 * $table = new Table
 * ([
 *     Progress::NAME    => 'PUB.Customer' ,
 *     Progress::OWNER   => 'PUB' ,
 *     Progress::TYPE    => 'T' ,
 *     Progress::CREATOR => 'sysprogress' ,
 * ]);
 * ```
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/OpenEdge-SQL-system-catalog-tables.html
 *
 * @package com\progress\schema\constants
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class Progress
{
    use Properties ;

    /**
     * The default JSON-LD `@context` URI for Progress OpenEdge system entities.
     */
    public const string SCHEMA = 'https://schema.progress.com' ;
}
