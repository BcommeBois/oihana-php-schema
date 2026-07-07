<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The channel through which a payment reminder is sent.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific channels (the
 * `OTHER` member is provided for any channel not listed here).
 *
 * | Constant | Description                            | Value                                                    |
 * |----------|----------------------------------------|----------------------------------------------------------|
 * | EMAIL    | An email reminder.                     | https://schema.oihana.xyz/PaymentReminderChannel#Email   |
 * | OTHER    | Any channel not covered by the others. | https://schema.oihana.xyz/PaymentReminderChannel#Other   |
 * | PHONE    | A phone-call reminder.                 | https://schema.oihana.xyz/PaymentReminderChannel#Phone   |
 * | POSTAL   | A postal-mail reminder.                | https://schema.oihana.xyz/PaymentReminderChannel#Postal  |
 * | SMS      | A text-message (SMS) reminder.         | https://schema.oihana.xyz/PaymentReminderChannel#Sms     |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class PaymentReminderChannel extends Enumeration
{
    /**
     * An email reminder.
     */
    public const string EMAIL = 'https://schema.oihana.xyz/PaymentReminderChannel#Email' ;

    /**
     * Any channel not covered by the others.
     */
    public const string OTHER = 'https://schema.oihana.xyz/PaymentReminderChannel#Other' ;

    /**
     * A phone-call reminder.
     */
    public const string PHONE = 'https://schema.oihana.xyz/PaymentReminderChannel#Phone' ;

    /**
     * A postal-mail reminder.
     */
    public const string POSTAL = 'https://schema.oihana.xyz/PaymentReminderChannel#Postal' ;

    /**
     * A text-message (SMS) reminder.
     */
    public const string SMS = 'https://schema.oihana.xyz/PaymentReminderChannel#Sms' ;
}
