<?php

namespace xyz\oihana\schema\traits;

use xyz\oihana\schema\constants\Oihana;

/**
 * Special Product additional properties.
 *
 * This trait exposes custom descriptive attributes extending the Schema.org Product model.
 * These fields can be hydrated from external datasets and exported as JSON-LD to enrich SEO and interoperability with structured data consumers.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait ProductProperty
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Accounting classification or regulatory category.
     *
     * @var mixed
     */
    public mixed $accounting ;

    /**
     * Visual appearance category (e.g. style, texture).
     *
     * @var mixed
     */
    public mixed $appearance ;

    /**
     * Main type of application or field of usage.
     *
     * @var mixed
     */
    public mixed $application ;

    /**
     * Assembly instructions or structural composition.
     *
     * @var mixed
     */
    public mixed $assembly ;

    /**
     * Certifications or conformity labels (e.g. CE).
     *
     * @var mixed
     */
    public mixed $certification ;

    /**
     * Available choise of the product.
     *
     * @var mixed
     */
    public mixed $choise ;

    /**
     * Available colors or finishes palette.
     *
     * @var mixed
     */
    public mixed $colors ;

    /**
     * Density classification or marking label.
     *
     * @var mixed
     */
    public mixed $densityLabel ;

    /**
     * Type of door edge (profile / machining details).
     *
     * @var mixed
     */
    public mixed $doorEdge ;

    /**
     * Durability, expected lifetime, or wear resistance.
     *
     * @var mixed
     */
    public mixed $durability ;

    /**
     * Wood essence or material primary origin.
     *
     * @var mixed
     */
    public mixed $essence ;

    /**
     * Fire resistance classification.
     *
     * @var mixed
     */
    public mixed $fireRating ;

    /**
     * Frame type, compatibility, or mounting structure.
     *
     * @var mixed
     */
    public mixed $frame ;

    /**
     * Surface finishing or coating (e.g. varnish).
     *
     * @var mixed
     */
    public mixed $finishing ;

    /**
     * Hardware and fittings configuration.
     *
     * @var mixed
     */
    public mixed $hardware ;

    /**
     * Installation instructions or mounting specificities.
     *
     * @var mixed
     */
    public mixed $installation ;

    /**
     * Type or kind within the product family taxonomy.
     *
     * @var mixed
     */
    public mixed $kind ;

    /**
     * Locking system or lock compatibility.
     *
     * @var mixed
     */
    public mixed $lock ;

    /**
     * Machining, CNC preparation, or modification options.
     *
     * @var mixed
     */
    public mixed $machining ;

    /**
     * Geographical or production origin.
     *
     * @var mixed
     */
    public mixed $origin ;

    /**
     * Opening direction, configuration, or mechanism.
     *
     * @var mixed
     */
    public mixed $opening ;

    /**
     * Performance properties (e.g. soundproofing).
     *
     * @var mixed
     */
    public mixed $performance ;

    /**
     * List of profiles, joints, and compatible components.
     *
     * @var mixed
     */
    public mixed $profiles ;

    /**
     * Quality level or market positioning.
     *
     * @var mixed
     */
    public mixed $quality ;

    /**
     * Thermal conductivity coefficient (W/m·K).
     *
     * @var mixed
     */
    public mixed $thermalConductivity ;

    /**
     * Thermal resistance coefficient (m²·K/W).
     *
     * @var mixed
     */
    public mixed $thermalResistance ;

    /**
     * Use class or environmental suitability category.
     *
     * @var mixed
     */
    public mixed $useClass ;
}