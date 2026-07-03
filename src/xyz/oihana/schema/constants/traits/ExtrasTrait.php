<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\extras\ContactPointType;
use xyz\oihana\schema\constants\traits\extras\GeoCoordinates;
use xyz\oihana\schema\constants\traits\extras\PostalAddress;

/**
 * The enumeration of all extra properties constants.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait ExtrasTrait
{
    use ContactPointType ,
        GeoCoordinates   ,
        PostalAddress    ;
}