<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\thesaurus\CollectionTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptSchemeTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait;
use xyz\oihana\schema\constants\traits\thesaurus\SkosMappingsTrait;
use xyz\oihana\schema\constants\traits\thesaurus\SkosNotesTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait;

/**
 * The enumeration of all Website properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait WebsiteTrait
{
    public const string HREF    = 'href' ;
    public const string PRIMARY = 'primary' ;
    public const string REL     = 'rel' ;
}
