<?php

namespace org\schema\places;

use org\schema\Duration;
use org\schema\Place;
use org\schema\QuantitativeValue;

/**
 * An accommodation is a place that can accommodate human beings, e.g. a hotel room, a camping pitch, or a meeting room. Many accommodations are for overnight stays, but this is not a mandatory requirement.
 * For more specific types of accommodations not defined in schema.org, one can use additionalType with external vocabularies.
 * @see https://schema.org/Accommodation
 */
class Accommodation extends Place
{
    /**
     * Category of an Accommodation, following real estate conventions, e.g. RESO (see PropertySubType, and PropertyType fields for suggested values).
     * @var string|null
     */
    public ?string $accommodationCategory = null;

    /**
     * A floor plan of some Accommodation.
     * @var object|array|null
     */
    public null|array|object $accommodationFloorPlan ; // TODO FloorPlan -> https://schema.org/FloorPlan

    /**
     * The type of bed or beds included in the accommodation.
     * @var object|array|null
     */
    public null|array|object $bed ; // TODO BedDetails -> https://schema.org/BedDetails

    /**
     * The floor level for an Accommodation in a multi-storey building.
     * Since counting systems vary internationally, the local system should be used where possible.
     * @var string|null
     */
    public ?string $floorLevel ;

    /**
     * The size of the accommodation, e.g. in square meter or squarefoot. Typical unit code(s): MTK for square meter, FTK for square foot, or YDK for square yard.
     * @var QuantitativeValue|null
     */
    public ?QuantitativeValue $floorSize ;

    /**
     * Length of the lease for some Accommodation, either particular to some Offer or in some cases intrinsic to the property.
     * @var QuantitativeValue|Duration|null
     */
    public null|QuantitativeValue|Duration $leaseLength ;

    /**
     * The total integer number of bathrooms in some Accommodation.
     * @var int|null
     */
    public ?int $numberOfBathroomsTotal ;

    /**
     * The total integer number of bedrooms in a some Accommodation, ApartmentComplex or FloorPlan.
     * @var int|QuantitativeValue|null
     */
    public null|int|QuantitativeValue $numberOfBedrooms ;

    /**
     * Number of full bathrooms - The total number of full and ¾ bathrooms in an Accommodation. This corresponds to the BathroomsFull field in RESO.
     * @var int|null
     */
    public null|int $numberOfFullBathrooms ;

    /**
     * Number of partial bathrooms - The total number of half and ¼ bathrooms in an Accommodation. This corresponds to the BathroomsPartial field in RESO.
     * @var int|null
     */
    public null|int $numberOfPartialBathrooms ;

    /**
     * The allowed total occupancy for the accommodation in persons (including infants etc).
     * For individual accommodations, this is not necessarily the legal maximum but defines the permitted usage as per the contractual agreement (e.g. a double room used by a single person).
     * Typical unit code(s): C62 for person.
     * @var QuantitativeValue|int|null
     */
    public null|QuantitativeValue|int $occupancy ;

    /**
     * Indications regarding the permitted usage of the accommodation.
     * @var string|null
     */
    public ?string $permittedUsage ;

    /**
     * Indicates whether pets are allowed to enter the accommodation or lodging business. More detailed information can be put in a text value.
     * @var string|bool|null
     */
    public null|bool|string $petsAllowed ;

    /**
     * The year an Accommodation was constructed. This corresponds to the YearBuilt field in RESO.
     * @var int|null
     */
    public ?int $yearBuilt ;
}


