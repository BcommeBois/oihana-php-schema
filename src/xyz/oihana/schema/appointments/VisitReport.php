<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateWith;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\VisitReportTrait;
use xyz\oihana\schema\people\CustomerEmployee;

/**
 * What was written after a meeting took place.
 *
 * A report answers, in the order a reader wants them : how it went ({@see VisitReport::$mood}),
 * what it produced ({@see VisitReport::$outcome}), what was discussed ({@see VisitReport::$topics}),
 * who was actually there ({@see VisitReport::$attendee}) — and, in the {@see CreativeWork::$text} it
 * inherits, everything the boxes cannot hold.
 *
 * It is a {@see CreativeWork} rather than an {@see \org\schema\Action} : it is a
 * piece of writing, with an author, a moment it was written and a body of text,
 * and it is read far more often than the meeting is replayed.
 *
 * 🔑 **The boxes and the text are not alternatives.** A report reduced to codes
 * loses what makes it worth reading ; a report reduced to prose cannot be counted.
 * Both are declared, neither is required : one written on a phone, in a van, with
 * three taps is worth more than the thorough one that never gets written.
 *
 * ⚠️ **Its attendees are not the meeting's.** {@see CustomerAppointment::$attendee} lists
 * who was expected ; this one lists who came. They disagree often enough that
 * folding them into one would quietly rewrite the plan into a record of fact.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class VisitReport extends CreativeWork
{
    use VisitReportTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Who actually attended.
     *
     * The people met, as codes or resolved contacts. May differ from
     * {@see CustomerAppointment::$attendee}, which says who was expected.
     *
     * @var null|array|Person
     * @since 1.5.0
     */
    #[HydrateWith(CustomerEmployee::class)]
    public null|array|Person $attendee ;

    /**
     * What comes next, and when.
     *
     * None, one or several promises : each says what is owed, when it is due, and —
     * once it is booked — the meeting made to honour it.
     *
     * @var null|array|FollowUp
     * @since 1.5.0
     */
    #[HydrateWith(FollowUp::class)]
    public null|array|FollowUp $followUp ;

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

    /**
     * Quick qualifiers of the report itself.
     *
     * Declared for the day one is needed ; the qualifiers of a meeting live on the meeting ({@see CustomerAppointment::$tags}),
     * where they are true whether they were planned or observed.
     *
     * @var null|array|string|DefinedTerm
     * @since 1.5.0
     */
    public null|array|string|DefinedTerm $tags ;

    /**
     * What was discussed.
     *
     * Terms of a controlled vocabulary : codes as published, or resolved terms.
     * Several, a meeting rarely covering one subject.
     *
     * @var null|array|string|DefinedTerm
     * @since 1.5.0
     */
    public null|array|string|DefinedTerm $topics ;
}
