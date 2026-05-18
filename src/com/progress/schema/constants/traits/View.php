<?php

namespace com\progress\schema\constants\traits ;

/**
 * Property keys mapped to the columns of the OpenEdge SQL `SYSVIEWS` system
 * catalog table.
 *
 * `SYSVIEWS` contains one row for each view defined in the database.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSVIEWS.html
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait View
{
    /**
     * Whether the view is a `WITH CHECK OPTION` view (`checkopt`).
     */
    public const string CHECK_OPTION = 'checkOption' ;

    /**
     * Total length in characters of the view definition text (`textlen`).
     */
    public const string TEXT_LENGTH = 'textLength' ;

    /**
     * `CREATE VIEW` SQL statement that defines the view (`text`).
     */
    public const string VIEW_TEXT = 'viewText' ;
}
