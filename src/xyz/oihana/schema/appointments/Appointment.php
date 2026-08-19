<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\DefinedTerm;
use org\schema\Event;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Place;
use org\schema\PostalAddress;
use org\schema\VirtualLocation;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\AppointmentTrait;
use xyz\oihana\schema\enumerations\AppointmentStatus;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\places\JobSite;

/**
 * A meeting arranged with a customer — on their premises, on a site, over the
 * phone, over video.
 *
 * It is an {@see Event} : something that happens at a moment, in a place, with
 * people. That is also what every calendar reads, which is why a diary needs to
 * be taught nothing to show one.
 *
 * 🔑 **Two axes of state, and they are not interchangeable.** The one inherited
 * from schema.org, {@see Event::$eventStatus}, says what became of the *slot* —
 * scheduled, moved, postponed, called off — and publishes no member for « it
 * happened ». {@see Appointment::$appointmentStatus} says what became of the
 * *meeting* : planned, done, nobody there, cancelled. A diary reads the first, a
 * report reads the second, and a single axis could not answer both.
 *
 * 🔑 **The customer is the one thing a meeting cannot do without.** Everything
 * else is optional : the contacts expected, the place, what one means to show.
 * A customer may be a known one — a reference and a frozen copy — or a free-form
 * one, a name and a telephone number, for the company that is not on the books
 * yet.
 *
 * ⚠️ **What is written before, and what is written after, sit side by side.**
 * `description`, {@see Appointment::$makesOffer} and {@see Appointment::$tags}
 * are the preparation ; {@see Appointment::$report} is what came of it. Neither
 * overwrites the other — a meeting is worth reading precisely for the distance
 * between the two.
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
     * What became of the meeting — planned, done, nobody there, cancelled.
     *
     * Reads beside {@see Event::$eventStatus}, which says what became of the slot.
     *
     * @var null|string|AppointmentStatus
     * @since 1.5.0
     */
    public null|string|AppointmentStatus $appointmentStatus ;

    /**
     * What kind of meeting it is — on the customer's premises, on a site, over the
     * phone, over video.
     *
     * A term of a controlled vocabulary : a code as published, or the resolved
     * term. One value.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $appointmentType ;

    /**
     * The salesperson the customer is attached to.
     *
     * A code, or the resolved person. Reuses the name, the shape and the meaning
     * {@see Customer::$assignedSeller} already carries — and may differ from
     * {@see Appointment::$organizer}, who is whoever holds *this* meeting.
     *
     * @var null|int|string|array|Person
     * @since 1.5.0
     */
    public null|int|string|array|Person $assignedSeller ;

    /**
     * The customer's contacts expected at the meeting.
     *
     * Redeclares {@see Event::$attendee} with the same union, to name who is meant :
     * a stored row is read back as a {@see CustomerEmployee}. Optional, none or
     * several.
     *
     * @var null|array|Person|Organization
     * @since 1.5.0
     */
    #[HydrateWith(CustomerEmployee::class)]
    public Person|Organization|array|null $attendee ;

    /**
     * The customer the meeting is with.
     *
     * A reference and a frozen copy of a known customer, or a free-form one — a
     * name, a telephone number — for a company not on the books yet. Reuses the
     * name and the shape
     * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$customer}
     * already carries.
     *
     * 🔑 **{@see Customer} is named first among the candidates**, so a stored row
     * carrying the type is read back with the properties only a customer has. A
     * class that is not named cannot be chosen, and what it alone declares is
     * dropped without a word.
     *
     * @var null|array|Organization|Person
     * @since 1.5.0
     */
    #[HydrateWith(Customer::class, Organization::class, Person::class)]
    public null|array|Organization|Person $customer ;

    /**
     * Where it takes place.
     *
     * Redeclares {@see Event::$location} with the same union, to name the places a
     * meeting is actually held in : a customer address, a site, or a virtual room.
     *
     * @var null|array|PostalAddress|Place|VirtualLocation|string
     * @since 1.5.0
     */
    #[HydrateWith(CustomerSite::class, JobSite::class, Place::class, PostalAddress::class, VirtualLocation::class)]
    public PostalAddress|Place|VirtualLocation|string|array|null $location ;

    /**
     * What the salesperson means to put in front of the customer.
     *
     * Each entry wraps one product in an {@see Offer} : `itemOffered` names the
     * product, `description` says what to do with it, and the price properties are
     * there the day an intention becomes a figure — a discount considered, a
     * quantity worth quoting. Reuses the name and the meaning
     * {@see Organization::$makesOffer} already carries.
     *
     * 🔑 **An intention, not a quote.** Nothing here commits anyone : the document that does is written elsewhere,
     * and points back at the meeting it came from.
     *
     * @var null|array|Offer
     * @since 1.5.0
     */
    #[HydrateWith(Offer::class)]
    public null|array|Offer $makesOffer ;

    /**
     * Whose diary this meeting is in — the salesperson who holds it.
     *
     * Redeclares {@see Event::$organizer} with the same union, to name the subject :
     * a stored row is read back as a {@see Seller}. Not necessarily whoever entered
     * it : an assistant books meetings for someone else's diary, and it is the
     * diary that matters here.
     *
     * @var null|array|Person|Organization
     * @since 1.5.0
     */
    #[HydrateAs(Seller::class)]
    public array|null|Person|Organization $organizer ;

    /**
     * What was written once the meeting took place.
     *
     * One report, absent until there is something to report.
     *
     * @var null|array|VisitReport
     * @since 1.5.0
     */
    #[HydrateAs(VisitReport::class)]
    public null|array|VisitReport $report ;

    /**
     * Quick qualifiers a salesperson ticks rather than writes — a meal with the
     * customer, a tour of their premises, a site visit, a demonstration.
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
