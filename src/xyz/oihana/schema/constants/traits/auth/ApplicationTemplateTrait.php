<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all ApplicationTemplate properties.
 *
 * Properties already available via other traits:
 * - COLOR (Properties)
 * - SYSTEM, PROTECTED (RoleTrait)
 * - SCOPES, SCOPES_COUNT (ScopeTrait)
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait ApplicationTemplateTrait
{
    const string APPLICATION_TEMPLATES       = 'applicationTemplates' ;
    const string APPLICATION_TEMPLATES_COUNT = 'applicationTemplatesCount' ;
    const string SELF_SERVICE                = 'selfService' ;
}
