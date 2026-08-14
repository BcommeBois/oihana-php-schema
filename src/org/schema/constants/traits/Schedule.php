<?php

namespace org\schema\constants\traits;

/**
 * The Schedule properties enumeration.
 */
trait Schedule
{
    const string BY_DAY            = 'byDay'            ;
    const string BY_MONTH          = 'byMonth'          ;
    const string BY_MONTH_DAY      = 'byMonthDay'       ;
    const string BY_MONTH_WEEK     = 'byMonthWeek'      ;
    const string DURATION          = 'duration'         ;
    const string END_DATE          = 'endDate'          ;
    const string END_TIME          = 'endTime'          ;
    const string EXCEPT_DATE       = 'exceptDate'       ;
    const string REPEAT_COUNT      = 'repeatCount'      ;
    const string REPEAT_FREQUENCY  = 'repeatFrequency'  ;
    const string SCHEDULE_TIMEZONE = 'scheduleTimezone' ;
    const string START_DATE        = 'startDate'        ;
    const string START_TIME        = 'startTime'        ;
}
