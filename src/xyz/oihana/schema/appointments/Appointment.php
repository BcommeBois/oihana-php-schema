<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\DefinedTerm;
use org\schema\Event;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\AppointmentTrait;
use xyz\oihana\schema\enumerations\AppointmentStatus;

/**
 * A meeting arranged with somebody — or with nobody outside.
 *
 * It is an {@see Event} : something that happens at a moment, in a place, with
 * people. That is also what every calendar reads, which is why a diary needs to
 * be taught nothing to show one.
 *
 * 🔑 **This is the one stored shape.** A visit to a customer, a meeting between
 * colleagues, a plain diary note : one class, one collection, one set of cases.
 * What tells a customer meeting apart is not a subclass — it is the stored type
 * of its counterpart. {@see Appointment::$about} names whom the meeting is with —
 * a customer, a supplier, nobody — and the frozen copy it holds carries its own
 * `additionalType`, which is what filters, facets and guards read. A meeting
 * requires a moment and a diary ; whether somebody sits on the other side is a
 * fact of the meeting, not a kind of it.
 *
 * 🔑 **One property, whatever it points at.** A facet and a grouping aim at one
 * property and one only : with a name per family, « how many meetings per
 * counterpart » would have no answer at all. Same reason
 * {@see \xyz\oihana\schema\statistics\Statistics::$about} carries the subject of
 * every kind of figure.
 *
 * 🔑 **Two axes of state, and they are not interchangeable.** The one inherited
 * from schema.org, {@see Event::$eventStatus}, says what became of the *slot* —
 * scheduled, moved, postponed, called off — and publishes no member for « it
 * happened ». {@see Appointment::$appointmentStatus} says what became of the
 * *meeting* : planned, done, nobody there, cancelled. A diary reads the first, a
 * report reads the second, and a single axis could not answer both.
 *
 * ⚠️ **What is written before, and what is written after, sit side by side.**
 * `description` and {@see Appointment::$tags} are the preparation ;
 * {@see Appointment::$report} is what came of it. Neither overwrites the other — a
 * meeting is worth reading precisely for the distance between the two.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class Appointment extends Event
{
    use AppointmentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Whom the meeting is with.
     *
     * Redeclares {@see Event::$about} with the same union, to name what it holds
     * here : the counterpart of the meeting — a reference and a frozen copy, or a
     * free-form value for something that is not on the books yet.
     *
     * 🔑 **No class is named on purpose.** Whom one meets is told by the value
     * itself — the frozen copy carries its own `additionalType` — so hydration
     * resolves on the stored type rather than on a declaration ; a meeting with
     * nobody outside simply leaves it empty.
     *
     * @var null|string|object|array
     * @since 1.5.0
     */
    public string|object|array|null $about ;

    /**
     * What became of the meeting — planned, done, nobody there, cancelled.
     *
     * Reads beside {@see Event::$eventStatus}, which says what became of the slot.
     *
     * @var null|array|string|AppointmentStatus
     * @since 1.5.0
     */
    public null|array|string|AppointmentStatus $appointmentStatus ;

    /**
     * What kind of meeting it is — on the premises, on a site, over the phone,
     * over video.
     *
     * A term of a controlled vocabulary : a code as published, or the resolved
     * term. One value.
     *
     * ⚠️ **Not to be confused with the counterpart.** This says what the meeting
     * *is like* ; whom it is *with* is {@see Appointment::$about}, whose stored
     * type is what tells a customer meeting apart.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $appointmentType ;

    /**
     * Who is expected at the meeting.
     *
     * Redeclares {@see Event::$attendee} with the same union. Optional, none or
     * several — like an ordinary diary, where one creates the event and then
     * invites.
     *
     * 🔑 **No class is named here either** : accounts of the house and contacts of
     * the counterpart may sit around the same table, so each entry resolves on
     * its own stored type.
     *
     * @var null|array|Person|Organization
     * @since 1.5.0
     */
    public Person|Organization|array|null $attendee ;

    /**
     * What one means to put in front of the counterpart — or came back with.
     *
     * Each entry wraps one product in an {@see Offer} : `itemOffered` names the
     * product, `description` says what to do with it, and the price properties are
     * there the day an intention becomes a figure — a discount considered, a
     * quantity worth quoting. Reuses the name and the meaning
     * {@see Organization::$makesOffer} already carries.
     *
     * Absent when there is nothing to present — which is most meetings, and every
     * plain diary note.
     *
     * @var null|array|Offer
     * @since 1.5.0
     */
    #[HydrateWith(Offer::class)]
    public null|array|Offer $makesOffer ;

    /**
     * Whose diary this meeting is in.
     *
     * Redeclares {@see Event::$organizer} with the same union, to name the subject :
     * a stored row is read back as a {@see User}.
     *
     * 🔑 **The account, not the business role.** A diary belongs to a person, and a
     * person may hold more than one role over time — a salesperson who also becomes
     * a sales manager keeps one diary and one set of hours. Hanging the meeting on
     * the role would mean merging two diaries the day the second one is granted.
     *
     * Not necessarily whoever entered it, either : an assistant books meetings for
     * someone else's diary, and it is the diary that matters here.
     *
     * @var null|array|Person|Organization
     * @since 1.5.0
     */
    #[HydrateAs(User::class)]
    public array|null|Person|Organization $organizer ;

    /**
     * What was written once the meeting took place.
     *
     * One report, absent until there is something to report. The report carries
     * its own stored type, and a write-up that says more than the common cases —
     * a visit's — resolves to its richer class from the value itself.
     *
     * @var null|array|MeetingReport
     * @since 1.5.0
     */
    #[HydrateAs(MeetingReport::class)]
    public null|array|MeetingReport $report ;

    /**
     * Quick qualifiers one ticks rather than writes — a meal, a tour of the
     * premises, a site visit, a demonstration.
     *
     * Terms of a controlled vocabulary : codes as published, or resolved terms.
     * Several. True of the meeting whether they were planned or observed, which is
     * why they live here and not on the report.
     *
     * @var null|array|string|DefinedTerm
     * @since 1.5.0
     */
    public null|array|string|DefinedTerm $tags ;
}
