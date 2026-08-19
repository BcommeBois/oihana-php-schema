<?php

namespace org\schema\constants\traits;

/**
 * The measurement technique properties enumeration.
 *
 * Named here once, as they are declared once in
 * {@see \org\schema\traits\MeasurementTechniqueTrait}, for every type that
 * carries a measure — Dataset, DataCatalog, DataDownload, PropertyValue,
 * Observation and StatisticalVariable.
 */
trait MeasurementTechnique
{
    const string MEASUREMENT_METHOD    = 'measurementMethod'    ;
    const string MEASUREMENT_TECHNIQUE = 'measurementTechnique' ;
}
