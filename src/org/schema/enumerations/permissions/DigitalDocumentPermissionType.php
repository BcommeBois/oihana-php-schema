<?php

namespace org\schema\enumerations\permissions;

use org\schema\Enumeration;

/**
 * A type of permission which can be granted for accessing a digital document.
 * @see https://schema.org/DigitalDocumentPermissionType
 */
class DigitalDocumentPermissionType extends Enumeration
{
    public const string COMMENT          = 'https://schema.org/CommentPermission';
    public const string READ_PERMISSION  = 'https://schema.org/ReadPermission' ;
    public const string WRITE_PERMISSION = 'https://schema.org/WritePermission' ;
}