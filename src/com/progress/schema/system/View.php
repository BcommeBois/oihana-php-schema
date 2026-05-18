<?php

namespace com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use org\schema\Thing ;

/**
 * Represents a row of the OpenEdge SQL `SYSVIEWS` system catalog table.
 *
 * `SYSVIEWS` contains one row for each view defined in the database, along
 * with the `CREATE VIEW` statement that defines it.
 *
 * @see https://docs.progress.com/bundle/openedge-sql-reference/page/SYSVIEWS.html
 *
 * @package com\progress\schema\system
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
class View extends Thing
{
    /**
     * The JSON-LD `@context` of the entity.
     */
    public const string CONTEXT = Progress::SCHEMA ;

    /**
     * Whether the view was created with the `WITH CHECK OPTION` clause
     * (`checkopt`).
     *
     * @var bool|null
     */
    public ?bool $checkOption ;

    /**
     * Identifier of the SQL user that created the view (`creator`).
     *
     * @var string|null
     */
    public ?string $creator ;

    /**
     * Total length in characters of the view definition text (`textlen`).
     *
     * @var int|null
     */
    public ?int $textLength ;

    /**
     * `CREATE VIEW` SQL statement that defines the view (`text`).
     *
     * @var string|null
     */
    public ?string $viewText ;
}
