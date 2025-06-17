<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * The business function specifies the type of activity or access (i.e., the bundle of rights) offered by the organization or business person through the offer.
 * Typical are sell, rental or lease, maintenance or repair, manufacture / produce, recycle / dispose, engineering / construction, or installation. Proprietary specifications of access rights are also instances of this class.
 * Commonly used values:
 * - http://purl.org/goodrelations/v1#ConstructionInstallation
 * - http://purl.org/goodrelations/v1#Dispose
 * - http://purl.org/goodrelations/v1#LeaseOut
 * - http://purl.org/goodrelations/v1#Maintain
 * - http://purl.org/goodrelations/v1#ProvideService
 * - http://purl.org/goodrelations/v1#Repair
 * - http://purl.org/goodrelations/v1#Sell
 * - http://purl.org/goodrelations/v1#Buy
 */
class BusinessFunction extends Enumeration
{
    public const string CONSTRUCTION_INSTALLATION = 'http://purl.org/goodrelations/v1#ConstructionInstallation';
    public const string DISPOSE = 'http://purl.org/goodrelations/v1#Dispose';
    public const string LEASE_OUT = 'http://purl.org/goodrelations/v1#LeaseOut';
    public const string MAINTAIN = 'http://purl.org/goodrelations/v1#Maintain';
    public const string PROVIDE_SERVICE = 'http://purl.org/goodrelations/v1#ProvideService';
    public const string REPAIR = 'http://purl.org/goodrelations/v1#Repair';
    public const string SELL = 'http://purl.org/goodrelations/v1#Sell';
    public const string BUY = 'http://purl.org/goodrelations/v1#Buy';
}


