<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;

/**
 * The enumeration of all thesaurus properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.1.1
 */
trait ThesaurusTrait
{
    use ConceptTrait ,
        ThesaurusTermTrait ;
}
