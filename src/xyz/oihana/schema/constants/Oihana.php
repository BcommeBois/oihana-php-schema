<?php

namespace xyz\oihana\schema\constants;

use org\schema\constants\traits\Properties;
use xyz\oihana\schema\constants\traits\AuditTrait;
use xyz\oihana\schema\constants\traits\AuthTrait;
use xyz\oihana\schema\constants\traits\BusinessTrait;
use xyz\oihana\schema\constants\traits\ExtrasTrait;
use xyz\oihana\schema\constants\traits\HttpTrait;
use xyz\oihana\schema\constants\traits\LogTrait;
use xyz\oihana\schema\constants\traits\OrganizationTrait;
use xyz\oihana\schema\constants\traits\PaginationTrait;
use xyz\oihana\schema\constants\traits\PeopleTrait;
use xyz\oihana\schema\constants\traits\places\Site;
use xyz\oihana\schema\constants\traits\ProductsTrait;
use xyz\oihana\schema\constants\traits\ThesaurusTrait;
use xyz\oihana\schema\constants\traits\WebsiteTrait;

/**
 * The enumeration of all the Oihana schema properties constants.
 *
 * Aggregates the domain specific constants traits (audit, auth, extras, http, log,
 * organizations, pagination, people, places, products, thesaurus, website) in a
 * single access point.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants
 * @since   1.3.0
 */
class Oihana
{
    /**
     * The default oihana schema.
     */
    public const string SCHEMA = 'https://schema.oihana.xyz';

    use Properties        , // All schema.org properties
        AuditTrait        ,
        AuthTrait         ,
        BusinessTrait     ,
        ExtrasTrait       ,
        HttpTrait         ,
        LogTrait          ,
        OrganizationTrait ,
        PaginationTrait   ,
        ProductsTrait     ,
        PeopleTrait       ,
        Site              ,
        ThesaurusTrait    ,
        WebsiteTrait      ;
}
