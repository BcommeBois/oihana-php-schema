<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\organizations\Company;
use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\traits\HasTradingMeasures;

/**
 * What one company traded over one year.
 *
 * A {@see Statistics} record whose subject is a company — commonly a
 * {@see Subsidiary} of the operator's own group, and just as commonly the group
 * itself read as one — carrying the same ten measures as every other family
 * ({@see HasTradingMeasures}). It is the figure a branch manager is answerable
 * for, and the one a director compares branches by.
 *
 * 🔑 **It is the one family whose subject is the perimeter.** Elsewhere
 * {@see Statistics::$assignedCompany} says which company a counterparty's figures
 * were measured for ; here that company *is* the subject, and the property is
 * left unset rather than repeating it. A reader looking for the perimeter of any
 * record reads `about` first and `assignedCompany` only when the two differ.
 *
 * ⚠️ **A group total and the sum of its members are not interchangeable.** A
 * record about the whole group is a reading in its own right, published as such,
 * and adding it to its members' records counts every figure twice. Whoever serves
 * them owes the distinction to whoever reads them — commonly by never mixing the
 * two in one sum.
 *
 * On the granularity a source may offer beyond the year : an ordered / invoiced /
 * delivered variant of the same period is a *qualification* of the measure, and
 * {@see \org\schema\Observation::$measurementQualifier} — inherited by
 * {@see ObservationSeries} — is where it belongs. No property of this class
 * carries it.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class CompanyStatistics extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The company these figures are about.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Company}, a bare code is
     * left as read. Typed at {@see Company} rather than at {@see Subsidiary},
     * which adds no term of its own : the same property then names a member of
     * the group and the group itself, and a stored `additionalType` still tells
     * a reader which of the two it is looking at.
     *
     * @var null|int|string|array|Thing
     * @since 1.4.0
     */
    #[HydrateAs(Company::class)]
    public null|int|string|array|Thing $about ;
}
