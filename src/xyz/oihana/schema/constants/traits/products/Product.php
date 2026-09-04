<?php

namespace xyz\oihana\schema\constants\traits\products ;

/**
 * The property name constants the {@see \xyz\oihana\schema\products\Product}
 * class adds to the Schema.org mirror.
 *
 * Only what the mirror does not already name : the inherited properties are covered
 * by {@see \org\schema\constants\traits\Product}, and the flat harvest keys by {@see ProductAdditionalProperty}.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\products
 * @since   1.4.0
 */
trait Product
{
    public const string FEES                    = 'fees' ;
    public const string HAS_APPLICABLE_RESOURCE = 'hasApplicableResource' ;
}
