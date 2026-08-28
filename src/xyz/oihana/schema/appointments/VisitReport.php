<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateWith;

use org\schema\DefinedTerm;
use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\VisitReportTrait;
use xyz\oihana\schema\people\CustomerEmployee;

/**
 * What a salesperson writes after a meeting with a customer.
 *
 * A {@see MeetingReport} like any other — a text, its promises, what was covered,
 * who came — plus the two things only a visit brings back : how it went
 * ({@see VisitReport::$mood}) and what it produced ({@see VisitReport::$outcome}).
 *
 * 🔑 **Those two are what a sales review reads**, and they are the reason this
 * class exists rather than the parent alone : « how many visits produced an
 * order » is a question no free text can answer.
 *
 * ⚠️ **Its attendees are the customer's staff**, which is what narrows the union
 * the parent leaves wide : a report of a visit is read back as the people of the
 * company one visited, never as somebody of ours.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class VisitReport extends MeetingReport
{
    use VisitReportTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Who actually attended.
     *
     * Redeclares {@see MeetingReport::$attendee} with the same union, to name who
     * is meant : a stored row is read back as a {@see CustomerEmployee}.
     *
     * @var null|array|Person
     * @since 1.5.0
     */
    #[HydrateWith(CustomerEmployee::class)]
    public null|array|Person $attendee ;

    /**
     * How the meeting felt — satisfied, neutral, a problem to deal with.
     *
     * A term of a controlled vocabulary : a code as published, or the resolved term.
     * One value, because a meeting has one climate.
     *
     * 🔑 **It is not the outcome.** A meeting can go well and produce nothing, and
     * a tense one can end in an order. Read together they say something neither
     * says alone ; folded into one they say less than either.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $mood ;

    /**
     * What the meeting produced — an order, a quote to write, something to follow up, nothing.
     *
     * A term of a controlled vocabulary : a code as published, or the resolved term. One value.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $outcome ;
}
