<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;

/**
 * A thesaurus term enriched with house-specific properties.
 *
 * Extends the schema.org {@see DefinedTerm} with local properties (e.g. `color`)
 * that are layered on top of harvested data coming from an external source.
 *
 * These properties survive a re-harvest because the harvest performs an AQL
 * `UPSERT ... UPDATE` merge that only rewrites the source fields and leaves the
 * untouched attributes in place.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\ThesaurusTerm;
 *
 * $term = new ThesaurusTerm
 * ([
 *     'name'              => 'Cabernet Sauvignon' ,
 *     'termCode'          => 'CAB-SAUV' ,
 *     ThesaurusTerm::COLOR => '#7B1E3A' ,
 * ]);
 * ```
 *
 * @see DefinedTerm
 * @see ThesaurusTermTrait
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class ThesaurusTerm extends DefinedTerm
{
    use ThesaurusTermTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * An optional house color, expressed as a `#RRGGBB` hex string.
     *
     * @var string|null
     */
    public ?string $color ;
}
