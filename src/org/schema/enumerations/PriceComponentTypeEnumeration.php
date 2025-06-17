<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * Enumerates different price components that together make up the total price for an offered product.
 * @see https://schema.org/PriceComponentTypeEnumeration
 */
class PriceComponentTypeEnumeration extends Enumeration
{
    /**
     * Represents the schema.org URL for "Activation fee" constant.
     * Note : frais d'activation (fr)
     */
    public const string ACTIVATION_FEE = 'https://schema.org/ActivationFee' ;

    /**
     * Represents the schema.org URL for "Cleaning fee" constant.
     * Note : frais de nettoyage (fr)
     */
    public const string CLEANING_FEE = 'https://schema.org/CleaningFee' ;

    /**
     * Represents the schema.org URL for "Distance fee" constant.
     * Note = frais kilométriques (fr)
     */
    public const string DISTANCE_FEE = 'https://schema.org/DistanceFee' ;

    /**
     * Represents the schema.org URL for "Down payment" constant.
     * Note : acompte (fr)
     */
    public const string DOWN_PAYMENT  = 'https://schema.org/Downpayment' ;

    /**
     * Represents the schema.org URL for "Installment“ constant.
     * Note : versement (fr)
     */
    public const string INSTALLMENT = 'https://schema.org/Installment' ;

    /**
     * Represents the schema.org URL for "Subscription" constant.
     * Note : abonnement (fr)
     */
    public const string SUBSCRIPTION  = 'https://schema.org/Subscription' ;
}