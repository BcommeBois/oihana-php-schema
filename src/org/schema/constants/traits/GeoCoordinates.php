<?php

namespace org\schema\constants\traits;

trait GeoCoordinates
{
    // ---- Standards

    public const string DISTANCE  = 'distance'  ;
    public const string ELEVATION = 'elevation' ;
    public const string LATITUDE  = 'latitude'  ;
    public const string LONGITUDE = 'longitude' ;

    // ---- Dot notation

    public const string DISTANCE_PATH  = 'geo.distance'  ;
    public const string ELEVATION_PATH = 'geo.elevation' ;
    public const string LATITUDE_PATH  = 'geo.latitude'  ;
    public const string LONGITUDE_PATH = 'geo.longitude' ;
}