<?php

namespace org\schema\organizations;

use org\schema\Organization;

/**
 * An enterprise (potentially individual but typically collaborative), planned to achieve a particular aim.
 *
 * Use properties from Organization, subOrganization/parentOrganization to indicate project sub-structures.
 *
 * @see https://schema.org/Project
 */
class Project extends Organization
{

}