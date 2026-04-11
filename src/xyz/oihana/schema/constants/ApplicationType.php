<?php
namespace xyz\oihana\schema\constants;

/**
 * OAuth2 application types supported.
 *
 * @package xyz\oihana\schema
 * @category Security / RBAC
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class ApplicationType
{
    // Zitadel
    public const string SPA    = 'spa' ;
    public const string NATIVE = 'native' ;
    public const string M2M    = 'm2m' ;
    public const string WEB    = 'web' ;
}