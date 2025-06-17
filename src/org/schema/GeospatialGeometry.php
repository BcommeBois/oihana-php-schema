<?php

namespace org\schema;

use org\schema\traits\GeospatialGeometryTrait;

/**
 * (Eventually to be defined as) a supertype of GeoShape designed to accommodate definitions from Geo-Spatial best practices.
 * @see https://schema.org/GeospatialGeometry
 */
class GeospatialGeometry extends Intangible
{
    use GeospatialGeometryTrait ;
}


