<?php

namespace xyz\oihana\schema\organizations;

/**
 * Represents a business entity associated with the group through membership or alliance.
 *
 * Unlike a strict legal subsidiary, an Affiliate represents a company that is part
 * of the corporate grouping or GIE (Groupement d'Intérêt Économique). It shares
 * the group's resources, branding, or ERP ecosystem while potentially maintaining
 * a more independent legal relationship than a direct daughter company.
 *
 * @see https://schema.org/Corporation
 * @see https://schema.org/memberOf
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\organizations
 * @since   1.3.0
 */
class Affiliate extends Company
{

}