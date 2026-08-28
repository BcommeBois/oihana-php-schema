<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateAs;

use org\schema\actions\ScheduleAction;
use org\schema\DefinedTerm;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\FollowUpTrait;

/**
 * What comes next after a meeting, and when.
 *
 * A {@see ScheduleAction} : schema.org already has the act of planning a future
 * thing, and with it the two moments that matter — `scheduledTime`, when the thing
 * is due, and `actionStatus`, whether it is still owed or has been honoured. The
 * agent is whoever owes it, the description says it in words.
 *
 * 🔑 **It is a promise, not an appointment.** « Call them back in a fortnight » is
 * owed by someone and has no slot ; the meeting booked to honour it, when there is
 * one, is named in {@see FollowUp::$result}. Writing the promise as a meeting would
 * put a placeholder in a diary and lose the distinction between what is agreed and
 * what is booked.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class FollowUp extends ScheduleAction
{
    use FollowUpTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * What kind of next step it is — call back, send the quote, visit again.
     *
     * A term of a controlled vocabulary : a code as published, or the resolved
     * term. One value.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $followUpType ;

    /**
     * The meeting booked to honour it, when there is one.
     *
     * Redeclares {@see \org\schema\Action::$result} with the same union, to name
     * what a follow-up produces.
     *
     * @var null|array|string|Thing
     * @since 1.5.0
     */
    #[HydrateAs(Appointment::class)]
    public null|Thing|array|string $result ;
}
