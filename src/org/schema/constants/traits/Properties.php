<?php

namespace org\schema\constants\traits;

/**
 * Enumeration of all properties defines in the schema.org and custom ontologies.
 */
trait Properties
{
    use AggregateOffer ,
        Brand ,
        Claim ,
        Common ,
        ConceptualObject ,
        CompoundPriceSpecification ,
        ContactPoint ,
        CreativeWork ,
        Dataset ,
        Edge ,
        Event ,
        GeoCoordinates ,
        GeospatialGeometry ,
        HoursSpecification ,
        I18n ,
        MediaObject ,
        Medias ,
        MediaSubscription ,
        Order ,
        Offer ,
        Organization ,
        Person ,
        Place ,
        PostalAddress ,
        PriceSpecification ,
        Service ,
        ShippingService ,
        Thing  ,
        UnitPriceSpecification ,
        Values ,
        WebAPI
        ;
}