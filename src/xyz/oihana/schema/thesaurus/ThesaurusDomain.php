<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusSchemeTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\thesaurus\traits\HasColor;

/**
 * A thesaurus domain : the top-level grouping of the schemes of a registry
 * (e.g. products, providers, shipping).
 *
 * In the registry layer of the model (registry → domains → schemes → concepts),
 * a domain is a pure filing shelf : it groups {@see ThesaurusScheme} entities
 * but is **not** a set of terms (hence {@see Intangible}, not `DefinedTermSet`).
 * The domain↔scheme link is carried by {@see ThesaurusScheme::$domain}, not by
 * the domain itself, and domains are **flat by design** — they do not nest.
 *
 * The labels are inherited from {@see \org\schema\Thing} : `name`, plus the
 * i18n-ready `alternateName` and `description` (`string|object|array|null`).
 *
 * The `ACTIVE`/`ORDER` name constants come from {@see ThesaurusSchemeTrait}
 * (shared with {@see ThesaurusScheme}) and `COLOR` from
 * {@see ThesaurusTermTrait}, mirroring how `ProductCategoryTerm` reuses the
 * shared keys.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\ThesaurusDomain;
 *
 * $domain = new ThesaurusDomain
 * ([
 *     'name'                  => 'Products' ,
 *     ThesaurusDomain::ACTIVE => true ,
 *     ThesaurusDomain::COLOR  => '#0EA5E9' ,
 *     ThesaurusDomain::ORDER  => 1 ,
 * ]);
 * ```
 *
 * @see Intangible
 * @see ThesaurusScheme
 * @see HasColor
 * @see ThesaurusSchemeTrait
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.3.0
 */
class ThesaurusDomain extends Intangible
{
    use HasColor             ,
        ThesaurusSchemeTrait ,
        ThesaurusTermTrait   ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The visibility flag : an inactive domain is hidden from the interfaces
     * without being deleted.
     *
     * @var bool|null
     */
    public ?bool $active ;

    /**
     * The display order of the domain among its siblings.
     *
     * @var int|null
     */
    public ?int $order ;
}
