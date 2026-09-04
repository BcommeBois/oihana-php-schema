<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The property name constants the {@see \xyz\oihana\schema\products\ApplicableResource}
 * class adds to the Schema.org mirror.
 *
 * 🚨 **`item` and `position` are deliberately absent.** They are inherited from
 * {@see \org\schema\ListItem} and already named by the mirror — declaring them
 * again here would put the same constant in two traits that
 * {@see \xyz\oihana\schema\constants\Oihana} composes side by side, and two
 * declarations that are not strictly identical are a **fatal error at class
 * composition**, not a warning.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.4.0
 */
trait ApplicableResource
{
    public const string APPLIED_BY_DEFAULT = 'appliedByDefault' ;
}
