<?php

namespace org\schema\constants\properties;

trait GeospatialGeometry
{
    const string GEO_CONTAINS   = 'geoContains' ;
    const string GEO_COVERED_BY = 'geoCoveredBy' ;
    const string GEO_COVERS     = 'geoCovers' ;
    const string GEO_CROSSES    = 'geoCrosses' ;
    const string GEO_DISJOINT   = 'geoDisjoint' ;
    const string GEO_EQUALS     = 'geoEquals' ;
    const string GEO_INTERSECTS = 'geoIntersects' ;
    const string GEO_OVERLAPS   = 'geoOverlaps' ;
    const string GEO_TOUCHES    = 'geoTouches' ;
    const string GEO_WITHIN     = 'geoWithin' ;
}