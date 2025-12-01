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
     * Billing / invoice support contact.
     */
    public const string BILLING = 'https://schema.oihana.xyz/ContactType#BillingSupport';

    /**
     * Customer service contact.
     */
    public const string CUSTOMER_SERVICE = 'https://schema.oihana.xyz/ContactType#CustomerService';

    /**
     * Fax contact.
     */
    public const string FAX = 'https://schema.oihana.xyz/ContactType#fax';

    /**
     * Landline phone contact.
     */
    public const string LANDLINE = 'https://schema.oihana.xyz/ContactType#landline';

    /**
     * Mobile phone contact.
     */
    public const string MOBILE = 'https://schema.oihana.xyz/ContactType#mobile';

    /**
     * Other or miscellaneous contact.
     */
    public const string OTHER = 'https://schema.oihana.xyz/ContactType#Other';

    /**
     * Sales contact.
     */
    public const string SALES = 'https://schema.oihana.xyz/ContactType#Sales';

    /**
     * Technical support contact.
     */
    public const string TECHNICAL_SUPPORT = 'https://schema.oihana.xyz/ContactType#TechnicalSupport';
}