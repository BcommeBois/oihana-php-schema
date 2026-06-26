<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\thesaurus\Collection} and
 * {@see \xyz\oihana\schema\thesaurus\OrderedCollection} schema entities.
 *
 * Aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait CollectionTrait
{
    /**
     * The attribute key of the skos:member property.
     */
    const string MEMBER = 'member' ;

    /**
     * The attribute key of the skos:memberList property.
     */
    const string MEMBER_LIST = 'memberList' ;
}
