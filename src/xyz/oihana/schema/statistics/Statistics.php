<?php

namespace xyz\oihana\schema\statistics;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\StatisticsRecordTrait;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;

/**
 * What a body of figures is about, and over which stretch of time.
 *
 * A statistics record answers four questions and no others : *whose* figures
 * these are ({@see Statistics::$about}), *which side of the trade* they were
 * measured on ({@see Statistics::$direction}), *which year* they cover
 * ({@see Statistics::$year}) and *at which step* the series inside them are cut
 * ({@see Statistics::$observationPeriod}). The figures themselves are not here :
 * each family of statistics composes the measures it carries, so that a
 * customer, an article and a company are described by the same head and by
 * whichever measures their source actually publishes.
 *
 * It is an {@see Intangible} rather than a {@see \org\schema\creativeWork\Dataset} :
 * a dataset is a body of structured information that gets published, catalogued
 * and downloaded, and carries the editorial terms that go with that. This is a
 * *reading* — one subject, one period — closer to the {@see \org\schema\Observation}
 * it is made of than to the corpus it may end up in.
 *
 * 🔑 **The subject is a reference, never a copy.** `about` and
 * `assignedCompany` hold what the source gives — a code — or the resolved
 * object, and a consumer joins on read. Freezing a whole customer inside a
 * yearly figure would give every recomputation a chance to disagree with the
 * customer record itself.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class Statistics extends Intangible
{
    use StatisticsRecordTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The entity these figures are about.
     *
     * Holds the raw reference as read from the source (a business code), or the
     * resolved subject. Left untyped beyond {@see Thing} on purpose : each
     * family names its own subject by redeclaring the property with the same
     * union and its own `#[HydrateAs]`, which is what lets
     * {@see \oihana\reflect\Reflection::hydrate()} read a joined row back as the
     * right class while a bare code is left as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.4.0
     */
    public null|int|string|array|Thing $about ;

    /**
     * The company these figures were measured for.
     *
     * A code, or the resolved organization. Statistics are commonly kept per
     * legal entity as well as consolidated over all of them ; keeping the entity
     * on the record is what lets a reader tell one from the other, and what a
     * consumer scopes a reader's perimeter by.
     *
     * Reuses the name, the shape and the meaning
     * {@see \xyz\oihana\schema\organizations\Customer::$assignedCompany} already
     * carries.
     *
     * @var null|int|string|array|Organization
     * @since 1.4.0
     */
    public null|int|string|array|Organization $assignedCompany ;

    /**
     * Which side of the trade these figures were measured on — sales or
     * purchases, from the operator's point of view.
     *
     * The same enumeration, the same name and the same meaning as
     * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$direction} :
     * a figure and the document it was computed from say it the same way.
     *
     * @var null|string|BusinessDocumentDirection
     * @since 1.4.0
     */
    public null|string|BusinessDocumentDirection $direction ;

    /**
     * The step the series inside this record are cut at, as an ISO 8601
     * duration — `P1M` for one value a month, `P1W` a week, `P1D` a day.
     *
     * Stated on the record rather than on each measure : every series of a given
     * record is cut the same way, and saying it twelve times is twelve chances
     * to disagree. Same term, same format as
     * {@see \org\schema\Observation::$observationPeriod}.
     *
     * @var null|string
     * @since 1.4.0
     */
    public null|string $observationPeriod ;

    /**
     * The calendar year these figures cover.
     *
     * An integer : a year is compared, sorted and grouped far more often than it
     * is printed, and `2025` does all three without a cast. The wider period a
     * record covers, when it is not a plain year, belongs to `temporalCoverage`
     * on whatever publishes it — not here.
     *
     * @var null|int
     * @since 1.4.0
     */
    public null|int $year ;
}
