<?php

namespace xyz\oihana\schema\appointments;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Place;
use org\schema\PostalAddress;
use org\schema\VirtualLocation;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\appointments\CustomerAppointmentTrait;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\places\JobSite;

/**
 * A meeting arranged with a customer — on their premises, on a site, over the
 * phone, over video.
 *
 * An {@see Appointment} like any other : a moment, a place, a diary, two axes of
 * state, and a report once it has happened. What a customer meeting adds is the
 * salesperson's side of it — who follows this customer, and what one means to put
 * in front of them.
 *
 * 🔑 **The customer sits in {@see Appointment::$about}**, redeclared here to name
 * what it holds : a reference and a frozen copy of a known customer, or a
 * free-form one — a name, a telephone number — for a company that is not on the
 * books yet.
 *
 * ⚠️ **What is written before, and what is written after, sit side by side.**
 * `description`, {@see CustomerAppointment::$makesOffer} and the tags are the
 * preparation ; the report is what came of it. Neither overwrites the other — a
 * meeting is worth reading precisely for the distance between the two.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class CustomerAppointment extends Appointment
{
    use CustomerAppointmentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The customer the meeting is with.
     *
     * Redeclares {@see Appointment::$about} with the same union, to name who is
     * meant. A reference and a frozen copy of a known customer, or a free-form one
     * — a name, a telephone number — for a company not on the books yet.
     *
     * 🔑 **{@see Customer} is named first among the candidates**, so a stored row
     * carrying the type is read back with the properties only a customer has. A
     * class that is not named cannot be chosen, and what it alone declares is
     * dropped without a word.
     *
     * @var null|string|object|array
     * @since 1.5.0
     */
    #[HydrateWith(Customer::class, Organization::class, Person::class)]
    public string|object|array|null $about ;

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
     * Redeclares {@see Appointment::$attendee} with the same union, to name who is
     * meant : a stored row is read back as a {@see CustomerEmployee}. Optional,
     * none or several.
     *
     * @var null|array|Person|Organization
     * @since 1.5.0
     */
    #[HydrateWith(CustomerEmployee::class)]
    public Person|Organization|array|null $attendee ;

    /**
     * Where it takes place.
     *
     * Redeclares {@see Appointment::$location} with the same union, to name the
     * places a customer meeting is actually held in : a customer address, a site,
     * or a virtual room.
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
     * What was written once the meeting took place.
     *
     * Redeclares {@see Appointment::$report} to name the class that carries what a
     * visit brings back : how it felt, and what it produced.
     *
     * @var null|array|MeetingReport
     * @since 1.5.0
     */
    #[HydrateAs(VisitReport::class)]
    public null|array|MeetingReport $report ;
}
