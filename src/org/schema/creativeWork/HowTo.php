<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\Duration;
use org\schema\items\HowToSection;
use org\schema\items\HowToStep;
use org\schema\items\HowToSupply;
use org\schema\items\HowToTool;
use org\schema\MonetaryAmount;
use org\schema\QuantitativeValue;

/**
 * Instructions that explain how to achieve a result by performing a sequence of steps.
 * @see https://schema.org/HowTo
 */
class HowTo extends CreativeWork
{
    /**
     * The estimated cost of the supply or supplies consumed when performing instructions.
     * @var string|MonetaryAmount|null
     */
    public null|string|MonetaryAmount $estimatedCost;

    /**
     * The length of time it takes to perform instructions or a direction (not including time to prepare the supplies), in ISO 8601 duration format.
     * @var Duration|int|float|null|string
     */
    public null|Duration|int|float|string $performTime ;

    /**
     * The length of time it takes to prepare the items to be used in instructions or a direction, in ISO 8601 duration format.
     * @var Duration|int|float|null|string
     */
    public null|Duration|int|float|string $prepTime ;

    /**
     * A single step item (as HowToStep, text, document, video, etc.) or a HowToSection.
     * @var CreativeWork|HowToSection|HowToStep|string|array|null
     */
    public null|array|CreativeWork|HowToSection|HowToStep|string $step ;

    /**
     * A sub-property of instrument. A supply consumed when performing instructions or a direction.
     * @var string|HowToSupply|null
     */
    public null|string|HowToSupply $supply ;

    /**
     * A sub property of instrument. An object used (but not consumed) when performing instructions or a direction.
     * @var string|HowToTool|null
     */
    public null|string|HowToTool $tool;

    /**
     * The total time required to perform instructions or a direction (including time to prepare the supplies), in ISO 8601 duration format.
     * @var Duration|int|float|null|string
     */
    public null|Duration|int|float|string $totalTime ;

    /**
     * The quantity that results by performing instructions. For example, a paper airplane, 10 personalized candles.
     * @var string|QuantitativeValue|null
     */
    public null|string|QuantitativeValue $yield;
}


