<?php

namespace xyz\oihana\schema\business\documents;

/**
 * A purchase order — the customer's confirmed commitment to buy, typically
 * following the acceptance of a {@see Quote}.
 *
 * A distinct, maison-layer sibling of the schema.org mirror's
 * {@see \org\schema\Order} (see {@see BusinessDocument} for why this
 * hierarchy does not extend the mirror). Carries no properties of its own
 * beyond {@see BusinessDocument} for this version.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class PurchaseOrder extends BusinessDocument
{

}
