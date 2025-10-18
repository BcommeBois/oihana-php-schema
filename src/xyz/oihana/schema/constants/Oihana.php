<?php

namespace xyz\oihana\schema\constants;

use xyz\oihana\schema\constants\traits\LogTrait;
use xyz\oihana\schema\constants\traits\PaginationTrait;

class Oihana
{
    /**
     * The default oihana schema.
     */
    public const string SCHEMA = 'https://schema.oihana.xyz';

    use LogTrait,
        PaginationTrait ;
}