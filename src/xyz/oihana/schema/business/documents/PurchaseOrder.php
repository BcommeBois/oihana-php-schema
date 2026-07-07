<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\traits\business\documents\PurchaseOrderTrait;

/**
 * A purchase order — the customer's confirmed commitment to buy, typically
 * following the acceptance of a {@see Quote}.
 *
 * A distinct, maison-layer sibling of the schema.org mirror's
 * {@see \org\schema\Order} (see {@see BusinessDocument} for why this
 * hierarchy does not extend the mirror).
 *
 * Carries the upstream link of the quote → purchase order → invoice cycle :
 * `referencesQuote` points at the {@see Quote}(s) the order originates from
 * — the counterpart of {@see Invoice::$referencesOrder} downstream, and the
 * data behind the {@see \xyz\oihana\schema\enumerations\BusinessDocumentStatus::CONVERTED}
 * transition.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class PurchaseOrder extends BusinessDocument
{
    use PurchaseOrderTrait ;

    /**
     * The quote(s) this purchase order originates from. One or more accepted
     * quotes may be aggregated into a single purchase order.
     * @var null|array|Quote
     */
    #[HydrateWith(Quote::class)]
    public null|array|Quote $referencesQuote ;
}
