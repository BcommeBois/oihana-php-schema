# `xyz\oihana\schema\business\documents` — Documents commerciaux

Le namespace `xyz\oihana\schema\business\documents` modélise le cycle **devis → bon de commande → facture** (et ses à-côtés : avoir, bon de livraison, reçu, relevé). Il réutilise le vocabulaire Schema.org partout où il existe (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) et s'inspire d'UBL 2.3 pour les concepts absents de Schema.org (ajustements de prix, ventilation de taxes, échéancier de paiement) — sans recopier les XSD UBL.

> 🇬🇧 This page is also available in [English](../../en/oihana/business-documents.md).

---

## État de ce namespace

Cette page documente les **objets de valeur transverses** (`TaxDetail`, `Adjustment`…), la **hiérarchie de documents** (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`) et l'**export** (`BusinessDocumentExporter`, `JsonLdExporter`). Restent à venir dans une prochaine version : `CreditNote`, `DeliveryNote`, `Receipt`, `Statement` ; cette page sera complétée en conséquence.

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
| Porter les propriétés communes à tous les documents commerciaux (parties, dates, montants, statut…). | [`BusinessDocument`](#businessdocument) |
| Représenter un devis. | [`Quote`](#quote) |
| Représenter un bon de commande. | [`PurchaseOrder`](#purchaseorder) |
| Représenter une facture. | [`Invoice`](#invoice) |
| Sérialiser un document commercial (JSON-LD, et demain UBL/Factur-X…). | [`BusinessDocumentExporter`](#businessdocumentexporter) / [`JsonLdExporter`](#jsonldexporter) |

Les objets de valeur (`TaxDetail`, `Adjustment`…) étendent `org\schema\StructuredValue` (comme `MonetaryAmount` ou `PriceSpecification`) : ce sont des valeurs structurées, pas des ressources adressables. `BusinessDocument` et ses déclinaisons (`Quote`, `PurchaseOrder`, `Invoice`) étendent `org\schema\Intangible` — voir [`BusinessDocument`](#businessdocument) pour la justification de cet ancrage. Toutes exposent le distinguisheur `@context = 'https://schema.oihana.xyz'`.

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

Un `Quote` complet, avec ses lignes et son échéancier, s'hydrate de la même façon :

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

$quote = new Reflection()->hydrate
([
    Quote::CURRENCY       => 'EUR' ,
    Quote::ISSUE_DATE     => '2026-01-15' ,
    Quote::VALID_THROUGH  => '2026-02-15' ,
    Quote::STATUS         => BusinessDocumentStatus::DRAFT ,
    Quote::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 5 ] ] ,
    Quote::TOTALS         => [ 'total' => [ 'value' => 120 , 'currency' => 'EUR' ] ] ,
], Quote::class);

$quote->documentLines[ 0 ] instanceof \xyz\oihana\schema\business\documents\BusinessDocumentLine ; // true
$quote->totals instanceof \xyz\oihana\schema\business\documents\DocumentTotals ;                    // true
```

Une `Invoice` référence le `PurchaseOrder` qu'elle facture (et non `org\schema\Order` — voir [`Invoice`](#invoice) pour la justification), puis s'exporte en JSON-LD via `JsonLdExporter` :

```php
use org\schema\enumerations\status\PaymentComplete;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\export\JsonLdExporter;

$invoice = new Invoice
([
    Invoice::CURRENCY       => 'EUR' ,
    Invoice::ACCOUNT_ID     => 'ACC-001' ,
    Invoice::PAYMENT_STATUS => PaymentComplete::class ,
]);

echo new JsonLdExporter()->export( $invoice );
// {"@type":"Invoice","@context":"https://schema.oihana.xyz","accountId":"ACC-001","currency":"EUR","paymentStatus":"org\\schema\\enumerations\\status\\PaymentComplete"}
```

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
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | Le parent commun du cycle devis → commande → facture : `attachments`, `currency`, `customer`, `documentLines`, `issueDate`, `paymentTerms`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`. Étend `Intangible` plutôt que de réutiliser `org\schema\Order`/`org\schema\Invoice` : un document commercial qualifie une transaction, ce n'est pas une ressource adressable en propre — et cela laisse le miroir Schema.org intact (les consommateurs existants d'`org\schema\Order`/`Invoice` ne voient aucun changement). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | Un devis — ajoute `validThrough` (réutilisation de la propriété Schema.org déjà portée par `PriceSpecification`/`Offer`, plutôt qu'un nouveau nom). À ne pas confondre avec `org\schema\creativeWork\Quotation`, qui est une **citation littéraire** sans rapport. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | Un bon de commande — l'engagement confirmé du client, typiquement après acceptation d'un `Quote`. Ne porte aucune propriété propre dans cette version. |
| <a id="invoice"></a>`Invoice` | `BusinessDocument` | Une facture — le document final du cycle devis → commande → facture : `accountId`, `billingPeriod`, `broker`, `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus` (→ `org\schema\enumerations\status\PaymentStatusType`, réutilisant ses classes membres existantes `PaymentComplete`/`PaymentDue`/`PaymentDeclined`/`PaymentPastDue`/`PaymentAutomaticallyApplied`), `provider`, `referencesOrder` (→ `PurchaseOrder`, propre à ce namespace), `scheduledPaymentDate`. Reprend les noms de propriétés de `org\schema\Invoice`, mais ne partage volontairement pas de trait de propriétés avec lui : `referencesOrder` doit pointer vers le `PurchaseOrder` maison (pas `org\schema\Order`), et certaines unions du miroir (`broker`, `category`, `billingPeriod`) datent d'avant la convention `null\|array\|X` — les élargir pour un trait commun reviendrait à modifier le miroir, ce que cette hiérarchie s'interdit (voir [`BusinessDocument`](#businessdocument)). |
| <a id="businessdocumentexporter"></a>`BusinessDocumentExporter` | *(interface)* | Le contrat de sérialisation d'un `BusinessDocument` : `export(BusinessDocument $document): string`. Les formats réglementaires (UBL, Factur-X, Peppol…) restent hors périmètre pour l'instant. |
| <a id="jsonldexporter"></a>`JsonLdExporter` | `BusinessDocumentExporter` | Implémentation de démonstration : délègue à `ThingTrait::jsonSerialize()` (héritée via `Intangible`/`Thing`) puis `json_encode()`. |

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
