<?php

namespace xyz\oihana\schema\constants;

use xyz\oihana\schema\constants\traits\AuditTrait;
use xyz\oihana\schema\constants\traits\AuthTrait;
use xyz\oihana\schema\constants\traits\LogTrait;
use xyz\oihana\schema\constants\traits\PaginationTrait;
use xyz\oihana\schema\constants\traits\places\SiteTrait;

class Oihana
{
    /**
     * The default oihana schema.
     */
    public const string SCHEMA = 'https://schema.oihana.xyz';

    use AuditTrait ,
        AuthTrait ,
        LogTrait,
        PaginationTrait ,
        SiteTrait ;
}