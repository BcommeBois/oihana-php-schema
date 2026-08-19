<?php

namespace xyz\oihana\schema\statistics;

use org\schema\Observation;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\ObservationSeriesTrait;

/**
 * One measure, read over a whole period and step by step inside it.
 *
 * An {@see Observation} states a figure ; a series states the same figure cut at
 * a regular step — a year of revenue as a total *and* as twelve monthly values.
 * The two live on the same object because they are the same measure : `value`
 * carries the total, {@see ObservationSeries::$values} the run, and the step
 * they are cut at is stated once, by whatever holds the series
 * ({@see Statistics::$observationPeriod}).
 *
 * 🔑 **A measure carries what its source states, and nothing else.** A total
 * with no run and a run with no total are both ordinary : a margin published
 * once a year has no monthly detail to give, and a cost published month by month
 * was never totalled. Neither absence is filled in here — a summed total and a
 * derived monthly figure look exactly like published ones once written, and
 * nothing downstream can tell them apart. The consumer sums or divides when it
 * needs to, and knows it did.
 *
 * The unit is `unitCode`, inherited from {@see \org\schema\QuantitativeValue} —
 * `MTQ` for cubic metres, `KGM` for kilograms, and the ISO 4217 code for a
 * monetary measure (`EUR`). A quantity states its unit ; a reader of the ten
 * measures of one record reads all ten the same way.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class ObservationSeries extends Observation
{
    use ObservationSeriesTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The measure, one value per step of the period it covers.
     *
     * A plain list, in chronological order and complete : for a year cut at
     * `P1M`, twelve entries, index 0 being January, and a step with nothing to
     * report holding a zero rather than being skipped — a shorter list would
     * leave a reader guessing which months it stands for.
     *
     * Absent when the source publishes a total only.
     *
     * @var null|array<int, int|float>
     * @since 1.4.0
     */
    public null|array $values ;
}
