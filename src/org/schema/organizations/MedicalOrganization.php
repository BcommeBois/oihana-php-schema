<?php

namespace org\schema\organizations;

use org\schema\enumerations\MedicalSpeciality;
use org\schema\Organization;

/**
 * A medical organization (physical or not), such as hospital, institution or clinic.
 *
 * @see https://schema.org/MedicalOrganization
 */
class MedicalOrganization extends Organization
{
    /**
     * Name or unique ID of network. (Networks are often reused across different insurance plans.)
     * @var string|null
     */
    public null|string $healthPlanNetworkId ;

    /**
     * Whether the provider is accepting new patients.
     * @var bool|null
     */
    public bool|null $isAcceptingNewPatients ;

    /**
     * A medical specialty of the provider.
     * @var array|string|MedicalSpeciality|null
     */
    public null|array|string|MedicalSpeciality $medicalSpecialty ;
}