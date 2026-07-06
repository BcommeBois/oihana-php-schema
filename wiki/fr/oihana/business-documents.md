# `xyz\oihana\schema\business\documents` — Documents commerciaux

Le namespace `xyz\oihana\schema\business\documents` modélise le cycle **devis → bon de commande → facture** (et ses à-côtés : avoir, bon de livraison, reçu, relevé). Il réutilise le vocabulaire Schema.org partout où il existe (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) et s'inspire d'UBL 2.3 pour les concepts absents de Schema.org (ajustements de prix, ventilation de taxes, échéancier de paiement) — sans recopier les XSD UBL.

> 🇬🇧 This page is also available in [English](../../en/oihana/business-documents.md).

---

## État de ce namespace

Cette page documente les **objets de valeur transverses** — les briques réutilisées par tous les documents commerciaux. La hiérarchie de documents elle-même (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`…) arrive dans une prochaine version ; cette page sera complétée en conséquence.

---

## Quand les utiliser

| Besoin | Classe |
|---|---|
| Ventiler une taxe (TVA, contribution) sur une ligne ou un document. | [`TaxDetail`](#taxdetail) |
| Appliquer une remise, majoration, des frais de port, une éco-participation, une consigne ou un emballage. | [`Adjustment`](#adjustment) |
| Définir la règle de calcul d'une éco-participation. | [`EcoFeeRule`](#ecofeerule) |
| Tracer l'application d'une règle d'éco-participation sur une ligne. | [`AppliedEcoFee`](#appliedecofee) |
| Résumer les montants d'un document (HT, taxes, TTC, acompte, reste dû). | [`DocumentTotals`](#documenttotals) |
| Représenter une ligne d'un document commercial. | [`BusinessDocumentLine`](#businessdocumentline) |
| Étaler un paiement sur plusieurs échéances. | [`PaymentSchedule`](#paymentschedule) / [`PaymentInstallment`](#paymentinstallment) |

Toutes ces classes étendent `org\schema\StructuredValue` (comme `MonetaryAmount` ou `PriceSpecification`) : ce sont des valeurs structurées, pas des ressources adressables. Elles exposent le distinguisheur `@context = 'https://schema.oihana.xyz'`.

---

## Exemple express

```php
use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\enumerations\PriceComponentType;

$line = new BusinessDocumentLine
([
    BusinessDocumentLine::POSITION => 1 ,
    BusinessDocumentLine::QUANTITY => 5 ,
    BusinessDocumentLine::TAXES       => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
    BusinessDocumentLine::ADJUSTMENTS =>
    [
        [ Adjustment::TYPE => PriceComponentType::DISCOUNT , Adjustment::PERCENTAGE => 10.0 ] ,
    ],
]);
```

Comme partout ailleurs dans la lib, le constructeur ne fait qu'une affectation brute : `$line->taxes[0]` reste un tableau tant qu'on ne passe pas par `new \oihana\reflect\Reflection()->hydrate(...)`, qui honore les attributs `#[HydrateWith]`/`#[HydrateAs]` de chaque classe et transforme les tableaux imbriqués en `TaxDetail`/`Adjustment`/`MonetaryAmount`.

---

## Catalogue des classes

| Classe | Étend | Rôle |
|---|---|---|
| <a id="taxdetail"></a>`TaxDetail` | `StructuredValue` | Une taxe (`category`, `rate`, `basisAmount`, `taxAmount`) appliquée à une ligne ou à un document. Ne mélange jamais TVA et contributions environnementales — voir `EcoFeeRule`/`AppliedEcoFee`. |
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | Un ajustement de prix (`type`, `amount` ou `percentage`, `reason`, `includedInBase`), inspiré d'UBL `AllowanceCharge`. Couvre remise, majoration, frais de port, éco-participation, consigne, emballage via la seule propriété `type` (voir `PriceComponentType`). |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | La règle de calcul d'une éco-participation (`category`, `rate`, `validFrom`, `validThrough`) — un concept de catalogue, sans effet monétaire propre. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | La trace d'application d'une `EcoFeeRule` sur une ligne (`rule`, `quantity`, `amount`) — l'effet monétaire réel passe toujours par un `Adjustment` de type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | Le récapitulatif monétaire d'un document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`), chaque montant en `MonetaryAmount`. Un objet dédié plutôt qu'une réutilisation de `CompoundPriceSpecification`, dont le rôle Schema.org (cumuler des prix qui s'appliquent en parallèle, ex. électricité + nettoyage) ne correspond pas à un récapitulatif HT/taxes/TTC. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | Une ligne de document (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`) — `taxes` et `adjustments` sont scopés à la ligne, un document peut donc mélanger des lignes à taux de TVA différents. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | Un échéancier de paiement (`installments`, une liste de `PaymentInstallment`). Version de base : les relances et le statut par échéance sont une itération ultérieure. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | Une échéance (`dueDate`, `amount` ou `percentage`). |

---

## Constantes associées

Chaque classe expose ses constantes de propriétés via un trait dédié sous [`constants/traits/business/documents/`](../../src/xyz/oihana/schema/constants/traits/business/documents), agrégés dans [`DocumentsTrait`](../../src/xyz/oihana/schema/constants/traits/business/DocumentsTrait.php), lui-même composé dans [`BusinessTrait`](../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php) puis dans l'agrégateur global [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) — contrairement à `BusinessIdentityTrait`/`UserProfileTrait`, aucune collision de nom n'a été détectée, donc ces constantes sont directement accessibles via `Oihana::RATE`, `Oihana::AMOUNT`, etc., en plus des constantes de classe (`TaxDetail::RATE`, `Adjustment::AMOUNT`…).

---

## Pour aller plus loin

- [`xyz\oihana\schema\business`](business.md) — `BusinessIdentity`, `UserProfile`.
- [`xyz\oihana\schema\products`](products.md) — `PriceComponentType`, réutilisée par `Adjustment::$type`.
- [`org\schema`](../schema-org/README.md) — `MonetaryAmount`, `PriceSpecification`, `StructuredValue`.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
