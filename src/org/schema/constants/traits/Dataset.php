<?php

namespace org\schema\constants\traits;

/**
 * The Dataset properties enumeration.
 *
 * The measurement terms it also carries are named by {@see MeasurementTechnique}.
 */
trait Dataset
{
    const string DISTRIBUTION             = 'distribution'          ;
    const string INCLUDED_IN_DATA_CATALOG = 'includedInDataCatalog' ;
    const string ISSN                     = 'issn'                  ;
    const string VARIABLE_MEASURED        = 'variableMeasured'      ;
}
