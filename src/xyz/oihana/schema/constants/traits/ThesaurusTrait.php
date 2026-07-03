<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\thesaurus\CollectionTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptSchemeTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait;
use xyz\oihana\schema\constants\traits\thesaurus\SkosMappingsTrait;
use xyz\oihana\schema\constants\traits\thesaurus\SkosNotesTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusSchemeTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait;

/**
 * The enumeration of all thesaurus properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.1.1
 */
trait ThesaurusTrait
{
    use CollectionTrait      ,
        ConceptSchemeTrait   ,
        ConceptTrait         ,
        SkosMappingsTrait    ,
        SkosNotesTrait       ,
        ThesaurusSchemeTrait ,
        ThesaurusTermTrait   ,
        TreeMetricsTrait     ;
}
