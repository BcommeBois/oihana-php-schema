<?php

namespace org\schema;

use DateTime;
use org\schema\creativeWork\HowTo;
use org\schema\enumerations\status\ActionStatusType;

/**
 * An action performed by a direct agent and indirect participants upon a direct object. Optionally happens at a location with the help of an inanimate instrument.
 * The execution of the action may produce a result. Specific action sub-type documentation specifies the exact expectation of each argument/role.
 * @see http://schema.org/Action
 */
class Action extends Thing
{
    /**
     * Description of the process by which the action was performed.
     * @var HowTo|null
     */
    public ?HowTo $actionProcess ;

    /**
     * Indicates the current disposition of the Action.
     * @var ActionStatusType|DefinedTerm|null
     */
    public null|ActionStatusType|DefinedTerm $actionStatus;

    /**
     * The direct performer or driver of the action (animate or inanimate). E.g. John wrote a book.
     * @var Person|Organization|null
     */
    public null|Person|Organization $agent ;

    /**
     * The endTime of something.
     * For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to end.
     * For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December.
     * For media, including audio and video, it's the time offset of the end of a clip within a larger file.
     * Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times.
     * This situation may be clarified in future revisions.
     */
    public null|string|int|DateTime $endTime ;

    /**
     * For failed actions, more information on the cause of the failure.
     * @var Thing|null
     */
    public ?Thing $error ;

    /**
     * The object that helped the agent perform the action. E.g. John wrote a book with a pen.
     * @var Thing|null
     */
    public ?Thing $instrument ;

    /**
     * The location of, for example, where an event is happening, where an organization is located, or where an action takes place.
     * @var Place|PostalAddress|string|VirtualLocation|null
     */
    public null|Place|PostalAddress|string|VirtualLocation $location ;

    /**
     * The object upon which the action is carried out, whose state is kept intact or changed. Also known as the semantic roles patient, affected or undergoer (which change their state) or theme (which doesn't). E.g. John read a book.
     * @var Thing|null
     */
    public ?Thing $object ;

    /**
     * Other co-agents that participated in the action indirectly. E.g. John wrote a book with Steve.
     * @var array|Person|Organization|null
     */
    public null|array|Person|Organization $participant ;

    /**
     * The service provider, service operator, or service performer; the goods producer.
     * Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var array|Person|Organization|null
     */
    public null|array|Person|Organization $provider ;

    /**
     * The result produced in the action. E.g. John wrote a book.
     * @var Thing|null
     */
    public ?Thing $result ;

    /**
     * The startTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to start. For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December. For media, including audio and video, it's the time offset of the start of a clip within a larger file.
     */
    public null|string|int|DateTime $startTime ;

    /**
     * Indicates a target EntryPoint, or url, for an Action.
     * @var string|EntryPoint|null
     */
    public null|string|EntryPoint $target ;
}


