<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\ContactPoint;
use org\schema\Enumeration;

/**
 * Types of contact points for an Organization or Person.
 *
 * Used for the `contactPoint.contactType` property in Schema.org.
 *
 * Each constant represents a type of contact that can be used to describe
 * the purpose or role of a contact point (phone, email, etc.) for an
 * Organization or Person.
 *
 * | Constant            | Description                                              | Value                                                    |
 * |---------------------|----------------------------------------------------------|----------------------------------------------------------|
 * | ASSISTANT           | Contact of an assistant or secretary.                    | https://schema.oihana.xyz/ContactType#Assistant          |
 * | BILLING             | Billing / invoice support contact.                       | https://schema.oihana.xyz/ContactType#BillingSupport     |
 * | CUSTOMER_SERVICE    | Customer service / support for general inquiries.        | https://schema.oihana.xyz/ContactType#CustomerService    |
 * | DEFAULT             | Default contact.                                         | https://schema.oihana.xyz/ContactType#Default            |
 * | EMERGENCY           | Emergency contact.                                       | https://schema.oihana.xyz/ContactType#Emergency          |
 * | FAX                 | Fax contact.                                             | https://schema.oihana.xyz/ContactType#Fax                |
 * | HOME                | Home / personal contact.                                 | https://schema.oihana.xyz/ContactType#Home               |
 * | LANDLINE            | Landline phone contact.                                  | https://schema.oihana.xyz/ContactType#Landline           |
 * | LEGAL               | Legal contact.                                           | https://schema.oihana.xyz/ContactType#Legal              |
 * | MANAGER             | Contact of a manager or executive.                       | https://schema.oihana.xyz/ContactType#Manager            |
 * | MEDIA               | Media contact.                                           | https://schema.oihana.xyz/ContactType#Media              |
 * | MOBILE              | Mobile phone contact.                                    | https://schema.oihana.xyz/ContactType#Mobile             |
 * | OTHER               | Other or miscellaneous contact.                          | https://schema.oihana.xyz/ContactType#Other              |
 * | PRESS               | Press / media inquiries.                                 | https://schema.oihana.xyz/ContactType#Press              |
 * | PUBLIC_RELATIONS    | Public relations contact.                                | https://schema.oihana.xyz/ContactType#PublicRelations    |
 * | SALES               | Sales contact for commercial inquiries.                  | https://schema.oihana.xyz/ContactType#Sales              |
 * | SOCIAL_MEDIAS       | Social media contact.                                    | https://schema.oihana.xyz/ContactType#SocialMedias       |
 * | SUPPORT             | Assistance / customer support / help desk contact (SAV). | https://schema.oihana.xyz/ContactType#Support            |
 * | TECHNICAL_SUPPORT   | Technical support contact, e.g., troubleshooting.        | https://schema.oihana.xyz/ContactType#TechnicalSupport   |
 * | WORK                | Work / business contact.                                 | https://schema.oihana.xyz/ContactType#Work               |
 *
 * @see ContactPoint
 */
class ContactType extends Enumeration
{
    /**
     * Assistant contact.
     */
    public const string ASSISTANT = 'https://schema.oihana.xyz/ContactType#Assistant';

    /**
     * Billing / invoice support contact.
     */
    public const string BILLING = 'https://schema.oihana.xyz/ContactType#BillingSupport';

    /**
     * Customer service contact.
     */
    public const string CUSTOMER_SERVICE = 'https://schema.oihana.xyz/ContactType#CustomerService';

    /**
     * Default contact.
     */
    public const string DEFAULT = 'https://schema.oihana.xyz/ContactType#Default';

    /**
     * Emergency contact.
     */
    public const string EMERGENCY = 'https://schema.oihana.xyz/ContactType#Emergency';

    /**
     * Fax contact.
     */
    public const string FAX = 'https://schema.oihana.xyz/ContactType#fax';

    /**
     * Home / personal contact.
     */
    public const string HOME = 'https://schema.oihana.xyz/ContactType#Home';

    /**
     * Landline phone contact.
     */
    public const string LANDLINE = 'https://schema.oihana.xyz/ContactType#landline';

    /**
     * Legal contact.
     */
    public const string LEGAL = 'https://schema.oihana.xyz/ContactType#Legal';

    /**
     * Manager / executive contact.
     */
    public const string MANAGER = 'https://schema.oihana.xyz/ContactType#Manager';

    /**
     * Media contact.
     */
    public const string MEDIA = 'https://schema.oihana.xyz/ContactType#Media';

    /**
     * Mobile phone contact.
     */
    public const string MOBILE = 'https://schema.oihana.xyz/ContactType#mobile';

    /**
     * Other or miscellaneous contact.
     */
    public const string OTHER = 'https://schema.oihana.xyz/ContactType#Other';

    /**
     * Press contact.
     */
    public const string PRESS = 'https://schema.oihana.xyz/ContactType#Press';

    /**
     * Public relations contact.
     */
    public const string PUBLIC_RELATIONS = 'https://schema.oihana.xyz/ContactType#PublicRelations';

    /**
     * Sales contact.
     */
    public const string SALES = 'https://schema.oihana.xyz/ContactType#Sales';

    /**
     * Social Medias contact.
     */
    public const string SOCIAL_MEDIAS = 'https://schema.oihana.xyz/ContactType#SocialMedias';

    /**
     * Support contact.
     */
    public const string SUPPORT = 'https://schema.oihana.xyz/ContactType#Support';

    /**
     * Technical support contact.
     */
    public const string TECHNICAL_SUPPORT = 'https://schema.oihana.xyz/ContactType#TechnicalSupport';

    /**
     * Work / business contact.
     */
    public const string WORK = 'https://schema.oihana.xyz/ContactType#Work';
}