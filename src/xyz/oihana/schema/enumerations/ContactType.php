<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\ContactPoint;
use org\schema\Enumeration;

/**
 * Types of contact points for an Organization or Person.
 *
 * Used for the `contactPoint.contactType` property.
 *
 * @see ContactPoint
 */
class ContactType extends Enumeration
{
    /**
     * Customer service contact.
     */
    public const string CUSTOMER_SERVICE = 'https://schema.oihana.xyz/ContactType#CustomerService';

    /**
     * Technical support contact.
     */
    public const string TECHNICAL_SUPPORT = 'https://schema.oihana.xyz/ContactType#TechnicalSupport';

    /**
     * Billing / invoice support contact.
     */
    public const string BILLING = 'https://schema.oihana.xyz/ContactType#BillingSupport';

    /**
     * Sales contact.
     */
    public const string SALES = 'https://schema.oihana.xyz/ContactType#Sales';

    /**
     * Mobile phone contact.
     */
    public const string MOBILE = 'https://schema.oihana.xyz/ContactType#mobile';

    /**
     * Landline phone contact.
     */
    public const string LANDLINE = 'https://schema.oihana.xyz/ContactType#landline';

    /**
     * Fax contact.
     */
    public const string FAX = 'https://schema.oihana.xyz/ContactType#fax';

    /**
     * Other or miscellaneous contact.
     */
    public const string OTHER = 'https://schema.oihana.xyz/ContactType#Other';
}