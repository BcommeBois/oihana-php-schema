<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The escalation level of a payment reminder, from a courteous nudge to a
 * formal, legally-weighted demand.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific levels.
 *
 * | Constant        | Description                                                     | Value                                                          |
 * |-----------------|-----------------------------------------------------------------|----------------------------------------------------------------|
 * | FINAL_NOTICE    | A last reminder before escalation.                              | https://schema.oihana.xyz/PaymentReminderLevel#FinalNotice     |
 * | FIRST_REMINDER  | The first formal reminder after the due date has passed.        | https://schema.oihana.xyz/PaymentReminderLevel#FirstReminder   |
 * | FORMAL_NOTICE   | The formal, legally-weighted demand for payment (mise en demeure). | https://schema.oihana.xyz/PaymentReminderLevel#FormalNotice |
 * | REMINDER        | A courteous reminder, before or just after the due date.        | https://schema.oihana.xyz/PaymentReminderLevel#Reminder        |
 * | SECOND_REMINDER | The second reminder, the payment still being outstanding.       | https://schema.oihana.xyz/PaymentReminderLevel#SecondReminder  |
 *
 * The natural escalation order is : REMINDER → FIRST_REMINDER → SECOND_REMINDER
 * → FINAL_NOTICE → FORMAL_NOTICE (the constants themselves are declared
 * alphabetically, per the library's convention).
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class PaymentReminderLevel extends Enumeration
{
    /**
     * A last reminder before escalation.
     */
    public const string FINAL_NOTICE = 'https://schema.oihana.xyz/PaymentReminderLevel#FinalNotice' ;

    /**
     * The first formal reminder after the due date has passed.
     */
    public const string FIRST_REMINDER = 'https://schema.oihana.xyz/PaymentReminderLevel#FirstReminder' ;

    /**
     * The formal, legally-weighted demand for payment (mise en demeure).
     */
    public const string FORMAL_NOTICE = 'https://schema.oihana.xyz/PaymentReminderLevel#FormalNotice' ;

    /**
     * A courteous reminder, before or just after the due date.
     */
    public const string REMINDER = 'https://schema.oihana.xyz/PaymentReminderLevel#Reminder' ;

    /**
     * The second reminder, the payment still being outstanding.
     */
    public const string SECOND_REMINDER = 'https://schema.oihana.xyz/PaymentReminderLevel#SecondReminder' ;
}
