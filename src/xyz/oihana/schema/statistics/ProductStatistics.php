<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\traits\HasTradingMeasures;

/**
 * What one article traded over one year.
 *
 * A {@see Statistics} record whose subject is a {@see Product}, carrying the same
 * ten measures as every other family ({@see HasTradingMeasures}). Read on the
 * sale side it answers what an article brings in and at which margin ; on the
 * purchase side, what it costs to keep in stock — the same record, told by
 * {@see Statistics::$direction}.
 *
 * 🔑 **This is the one family where `quantity` is worth its total.** Elsewhere a
 * quantity spanning several articles adds square metres to cubic metres to
 * pieces ; here every figure counts the same article in the same unit, and the
 * run and its total both mean something.
 *
 * It carries no dimension of its own : what an article belongs to — its family,
 * its category — lives on the article, and stays there. The company the figures
 * were measured for is on the record already, as
 * {@see Statistics::$assignedCompany}.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class ProductStatistics extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The article these figures are about.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Product}, a bare code is
     * left as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.4.0
     */
    #[HydrateAs(Product::class)]
    public null|int|string|array|Thing $about ;
}
