<?php

namespace org\schema\constants\traits;

trait PostalAddress
{
    const string ADDRESS = 'address' ;

    const string ADDITIONAL_ADDRESS     = 'addressName' ;
    const string ADDRESS_COUNTRY        = 'addressCountry' ;
    const string ADDRESS_DEPARTMENT     = 'addressDepartment' ;
    const string ADDRESS_LOCALITY       = 'addressLocality' ;
    const string ADDRESS_REGION         = 'addressRegion' ;
    const string POST_OFFICE_BOX_NUMBER = 'postOfficeBoxNumber' ;
    const string POSTAL_CODE            = 'postalCode' ;
    const string POSTAL_CODE_PREFIX     = 'postalCodePrefix' ;
    const string POSTAL_CODE_RANGE      = 'postalCodeRange' ;
    const string STREET_ADDRESS         = 'streetAddress' ;

    const string FULL_ADDITIONAL_ADDRESS     = 'address.additionalAddress' ;
    const string FULL_ADDRESS_COUNTRY        = 'address.addressCountry' ;
    const string FULL_ADDRESS_DEPARTMENT     = 'address.addressDepartment' ;
    const string FULL_ADDRESS_LOCALITY       = 'address.addressLocality' ;
    const string FULL_ADDRESS_REGION         = 'address.addressRegion' ;
    const string FULL_POSTAL_CODE            = 'address.postalCode' ;
    const string FULL_POST_OFFICE_BOX_NUMBER = 'address.postOfficeBoxNumber' ;
    const string FULL_STREET_ADDRESS         = 'address.streetAddress' ;
}