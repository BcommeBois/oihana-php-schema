<?php

namespace org\schema\constants\properties;

/**
 * Enumeration of all properties defines in the schema.org and custom ontologies.
 */
trait Properties
{
    use AggregateOffer ,
        Brand ,
        Common ,
        ConceptualObject ,
        CompoundPriceSpecification ,
        ContactPoint ,
        Edge ,
        Event ,
        GeoCoordinates ,
        GeospatialGeometry ,
        HoursSpecification ,
        I18n ,
        Medias ,
        Order ,
        Offer ,
        Organization ,
        Person ,
        Place ,
        PostalAddress ,
        PriceSpecification ,
        Medias ,
        Service ,
        ShippingService ,
        Thing  ,
        UnitPriceSpecification ,
        Values ,
        WebAPI
        ;
}