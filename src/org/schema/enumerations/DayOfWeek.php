<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * The day of the week, e.g. used to specify to which day the opening hours of an OpeningHoursSpecification refer.
 * Originally, URLs from GoodRelations were used (for Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday plus a special entry for PublicHolidays); these have now been integrated directly into schema.org.
 * @see https://schema.org/DayOfWeek
 */
class DayOfWeek extends Enumeration
{
    public const string MONDAY = 'http://purl.org/goodrelations/v1#Monday';
    public const string TUESDAY = 'http://purl.org/goodrelations/v1#Tuesday';
    public const string WEDNESDAY = 'http://purl.org/goodrelations/v1#Wednesday';
    public const string THURSDAY = 'http://purl.org/goodrelations/v1#Thursday';
    public const string FRIDAY = 'http://purl.org/goodrelations/v1#Friday';
    public const string SATURDAY = 'http://purl.org/goodrelations/v1#Saturday';
    public const string SUNDAY = 'http://purl.org/goodrelations/v1#Sunday';
    public const string PUBLIC_HOLIDAYS = 'http://purl.org/goodrelations/v1#PublicHolidays';
}