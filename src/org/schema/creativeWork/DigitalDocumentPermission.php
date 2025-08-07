<?php

namespace org\schema\creativeWork;

use org\schema\Audience;
use org\schema\ContactPoint;
use org\schema\enumerations\permissions\DigitalDocumentPermissionType;
use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;

/**
 * A permission for a particular person or group to access a particular file.
 *
 * @see https://schema.org/DigitalDocumentPermission
 */
class DigitalDocumentPermission extends Intangible
{
    /**
     * The person, organization, contact point, or audience that has been granted this permission.
     */
    public null|array|Audience|ContactPoint|Organization|Person $grantee ;

    /**
     * The type of permission granted the person, organization, or audience.
     * @var null|array|DigitalDocumentPermissionType
     */
    public null|array|DigitalDocumentPermissionType $permissionType ;
}