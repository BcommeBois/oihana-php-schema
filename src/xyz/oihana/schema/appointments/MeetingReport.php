<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateWith;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\MeetingReportTrait;

/**
 * What was written after a meeting took place.
 *
 * A report answers, in the order a reader wants them : what was discussed
 * ({@see MeetingReport::$topics}), who was actually there
 * ({@see MeetingReport::$attendee}), what remains to be done
 * ({@see MeetingReport::$followUp}) — and, in the {@see CreativeWork::$text} it
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
 * 🔑 **What a report stores has nothing to do with who the meeting was with.** A
 * text, its promises, what was covered, who came — an appointment of any kind
 * writes the same things afterwards, which is why this class sits above
 * {@see VisitReport} rather than beside it. What a **visit** adds is the pair of
 * vocabularies a salesperson brings back : how it felt, and what it produced.
 *
 * ⚠️ **Its attendees are not the meeting's.** An appointment lists who was
 * expected ; this one lists who came. They disagree often enough that folding
 * them into one would quietly rewrite the plan into a record of fact. Which class
 * they are read back as depends on the kind of meeting, so the union is left wide
 * here and narrowed by whoever knows.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class MeetingReport extends CreativeWork
{
    use MeetingReportTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Who actually attended.
     *
     * The people met, as codes or resolved persons. May differ from the attendees
     * of the meeting itself, which says who was expected.
     *
     * 🔑 **No class is named here on purpose.** Who sits at a table depends on the
     * kind of meeting — a customer's staff, a colleague — so each family narrows
     * the union with its own hydration, and a report read on its own is left as it
     * stands rather than read back as the wrong kind of person.
     *
     * @var null|array|Person
     * @since 1.5.0
     */
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
     * Quick qualifiers of the report itself.
     *
     * Declared for the day one is needed ; the qualifiers of a meeting live on the
     * meeting, where they are true whether they were planned or observed.
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
