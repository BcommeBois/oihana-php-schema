<?php

namespace org\schema\organizations;

use org\schema\Organization;

/**
 * Organization: A business corporation.
 *
 * @see https://schema.org/Corporation
 */
class Corporation extends Organization
{
    /**
     * The exchange traded instrument associated with a Corporation object.
     *
     * The tickerSymbol is expressed as an exchange and an instrument name separated by a space character.
     *
     * For the exchange component of the tickerSymbol attribute, we recommend using
     * the controlled vocabulary of Market Identifier Codes (MIC) specified in ISO 15022.
     *
     * @var string|null
     */
    public ?string $tickerSymbol ;
}