<?php

namespace org\schema\creativeWork\medias ;

use org\schema\creativeWork\MediaObject;
use org\schema\traits\MeasurementTechniqueTrait;

/**
 * All or part of a Dataset in downloadable form.
 * @see https://schema.org/DataDownload
 */
class DataDownload extends MediaObject
{
    use MeasurementTechniqueTrait ;
}
