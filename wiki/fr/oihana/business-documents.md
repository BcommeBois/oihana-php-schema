# `xyz\oihana\schema\business\documents` — Documents commerciaux

Le namespace `xyz\oihana\schema\business\documents` modélise le cycle **devis → bon de commande → facture** (et ses à-côtés : avoir, bon de livraison, reçu, relevé). Il réutilise le vocabulaire Schema.org partout où il existe (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) et s'inspire d'UBL 2.3 pour les concepts absents de Schema.org (ajustements de prix, ventilation de taxes, échéancier de paiement) — sans recopier les XSD UBL.

> 🇬🇧 This page is also available in [English](../../en/oihana/business-documents.md).

---

## État de ce namespace

Cette page documente l'intégralité du namespace : les **objets de valeur transverses** (`TaxDetail`, `Adjustment`, `PaymentReminder`, `DeliveryLine`, `ProofOfDelivery`, `AgingSummary`…), la **hiérarchie de documents** complète (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement`) et l'**export** (`BusinessDocumentExporter`, `JsonLdExporter`).

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
| Tracer les relances de paiement (rappels envoyés au client). | [`PaymentReminder`](#paymentreminder) |
| Porter les propriétés communes à tous les documents commerciaux (parties, dates, montants, statut…). | [`BusinessDocument`](#businessdocument) |
| Représenter un devis. | [`Quote`](#quote) |
| Représenter un bon de commande. | [`PurchaseOrder`](#purchaseorder) |
| Représenter une facture. | [`Invoice`](#invoice) |
| Représenter un avoir corrigeant une facture. | [`CreditNote`](#creditnote) |
| Représenter un bon de livraison. | [`DeliveryNote`](#deliverynote) |
| Détailler, ligne par ligne, ce qui a réellement été livré (par rapport à ce qui était commandé). | [`DeliveryLine`](#deliveryline) |
| Consigner la confirmation de réception d'une livraison (signataire, date, écart constaté). | [`ProofOfDelivery`](#proofofdelivery) |
| Représenter un reçu de paiement. | [`Receipt`](#receipt) |
| Représenter un relevé de compte périodique. | [`Statement`](#statement) / [`StatementEntry`](#statemententry) |
| Répartir un solde client par ancienneté de retard (balance âgée). | [`AgingSummary`](#agingsummary) |
| Sérialiser un document commercial (JSON-LD, et demain UBL/Factur-X…). | [`BusinessDocumentExporter`](#businessdocumentexporter) / [`JsonLdExporter`](#jsonldexporter) |

Les objets de valeur (`TaxDetail`, `Adjustment`…, ainsi que `StatementEntry`) étendent `org\schema\StructuredValue` (comme `MonetaryAmount` ou `PriceSpecification`) : ce sont des valeurs structurées, pas des ressources adressables. `BusinessDocument` et ses déclinaisons (`Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement`) étendent `org\schema\Intangible` — voir [`BusinessDocument`](#businessdocument) pour la justification de cet ancrage. Toutes exposent le distinguisheur `@context = 'https://schema.oihana.xyz'`.

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

Un `Quote` complet, avec ses lignes, une **remise appliquée au document entier** et son récapitulatif, s'hydrate de la même façon :

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;
use xyz\oihana\schema\enumerations\PriceComponentType;

$quote = new Reflection()->hydrate
([
    Quote::CURRENCY       => 'EUR' ,
    Quote::ISSUE_DATE     => '2026-01-15' ,
    Quote::VALID_THROUGH  => '2026-02-15' ,
    Quote::STATUS         => BusinessDocumentStatus::DRAFT ,
    Quote::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 5 ] ] ,
    Quote::ADJUSTMENTS    =>
    [
        [ 'type' => PriceComponentType::DISCOUNT , 'percentage' => 5 , 'reason' => 'Order-level discount' ] ,
    ],
    Quote::TOTALS =>
    [
        'total'          => [ 'value' => 114 , 'currency' => 'EUR' ] ,
        'allowanceTotal' => [ 'value' => 6   , 'currency' => 'EUR' ] ,
    ],
], Quote::class);

$quote->documentLines[ 0 ] instanceof \xyz\oihana\schema\business\documents\BusinessDocumentLine ; // true
$quote->adjustments[ 0 ]   instanceof \xyz\oihana\schema\business\documents\Adjustment ;            // true
$quote->totals             instanceof \xyz\oihana\schema\business\documents\DocumentTotals ;        // true
```

**Ligne vs document.** Un `Adjustment` s'applique soit à une ligne (`BusinessDocumentLine::$adjustments` — remise propre à un article), soit au document entier (`BusinessDocument::$adjustments` — remise de pied de document, forfait de port ou emballage facturés globalement), sur le modèle d'UBL `AllowanceCharge`. L'effet cumulé des ajustements de niveau document se relit, si besoin, dans les champs dérivés optionnels `DocumentTotals::$allowanceTotal` (total des remises) et `DocumentTotals::$chargeTotal` (total des majorations/frais), miroirs des `AllowanceTotalAmount`/`ChargeTotalAmount` d'UBL.

**Le chaînage du cycle.** Chaque maillon référence le document amont via une propriété `references*` : `PurchaseOrder::$referencesQuote` (→ `Quote`), `Invoice::$referencesOrder` (→ `PurchaseOrder`), `CreditNote`/`Receipt::$referencesInvoice` (→ `Invoice`). Ces liens sont **des collections** : chacun accepte un document unique **ou** plusieurs (facture de regroupement, paiement soldant plusieurs factures, commande agrégeant plusieurs devis). L'hydratation profonde est polymorphe — un tableau associatif unique donne un objet, une liste donne un tableau d'objets :

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\business\documents\Quote;

// Un bon de commande issu d'un devis accepté.
$order = new Reflection()->hydrate
([
    PurchaseOrder::CURRENCY         => 'EUR' ,
    PurchaseOrder::REFERENCES_QUOTE => [ Quote::CURRENCY => 'EUR' ] , // un seul devis → un objet Quote
], PurchaseOrder::class);

$order->referencesQuote instanceof Quote ; // true

// Une facture de regroupement soldant deux bons de commande.
$invoice = new Reflection()->hydrate
([
    Invoice::CURRENCY         => 'EUR' ,
    Invoice::REFERENCES_ORDER =>
    [
        [ PurchaseOrder::CURRENCY => 'EUR' ] ,
        [ PurchaseOrder::CURRENCY => 'EUR' ] ,
    ], // une liste → un tableau de PurchaseOrder
], Invoice::class);

is_array( $invoice->referencesOrder ) && count( $invoice->referencesOrder ) === 2 ; // true
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

Un `Statement` récapitule les documents qui ont fait bouger le solde d'un compte sur une période, via une liste de `StatementEntry`. Chaque ligne peut porter son **type** (facture, paiement, avoir…) et son **échéance**, et le relevé peut exposer une **balance âgée** (`AgingSummary`) — la répartition du solde par ancienneté de retard, que le consommateur calcule et renseigne :

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\AgingSummary;
use xyz\oihana\schema\business\documents\Statement;
use xyz\oihana\schema\business\documents\StatementEntry;
use xyz\oihana\schema\enumerations\StatementEntryType;

$statement = new Reflection()->hydrate
([
    Statement::OPENING_BALANCE => [ 'value' => 0   , 'currency' => 'EUR' ] ,
    Statement::CLOSING_BALANCE => [ 'value' => 120 , 'currency' => 'EUR' ] ,
    Statement::TOTAL_DEBIT     => [ 'value' => 120 , 'currency' => 'EUR' ] ,
    Statement::ENTRIES =>
    [
        [
            StatementEntry::TYPE     => StatementEntryType::INVOICE ,
            StatementEntry::DATE     => '2026-01-15' ,
            StatementEntry::DUE_DATE => '2026-02-15' ,
            StatementEntry::DOCUMENT => 'INV-001' ,
            StatementEntry::AMOUNT   => [ 'value' => 120 , 'currency' => 'EUR' ] ,
        ],
    ],
    Statement::AGING_SUMMARY =>
    [
        AgingSummary::CURRENT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
        AgingSummary::OVER_90 => [ 'value' => 20  , 'currency' => 'EUR' ] ,
    ],
], Statement::class);

$statement->entries[ 0 ] instanceof StatementEntry ;       // true
$statement->agingSummary instanceof AgingSummary ;         // true
```

Le `amount` signé reste la valeur de mouvement suffisante ; `debitAmount`/`creditAmount` sont une ventilation débit/crédit **optionnelle** (colonnes séparées à la UBL / comptabilité en partie double), en complément, pas en remplacement.

Une échéance en retard peut porter ses **relances** — la trace des rappels envoyés, avec leurs frais éventuels exprimés en `Adjustment` (jamais un champ « pénalité » dédié) :

```php
use oihana\reflect\Reflection;
use org\schema\enumerations\status\CompletedActionStatus;
use xyz\oihana\schema\business\documents\PaymentInstallment;
use xyz\oihana\schema\business\documents\PaymentReminder;
use xyz\oihana\schema\enumerations\PaymentReminderChannel;
use xyz\oihana\schema\enumerations\PaymentReminderLevel;

$installment = new Reflection()->hydrate
([
    PaymentInstallment::DUE_DATE  => '2026-02-01' ,
    PaymentInstallment::REMINDERS =>
    [
        [
            PaymentReminder::DATE          => '2026-02-20' ,
            PaymentReminder::LEVEL         => PaymentReminderLevel::SECOND_REMINDER ,
            PaymentReminder::CHANNEL       => PaymentReminderChannel::EMAIL ,
            PaymentReminder::STATUS        => CompletedActionStatus::class ,
            PaymentReminder::AMOUNT_CLAIMED => [ 'value' => 120 , 'currency' => 'EUR' ] ,
            PaymentReminder::ADJUSTMENTS   =>
            [
                [ 'type' => 'surcharge' , 'reason' => 'Late fee' , 'amount' => [ 'value' => 40 , 'currency' => 'EUR' ] ] ,
            ],
        ],
    ],
], PaymentInstallment::class);

$installment->reminders[ 0 ] instanceof PaymentReminder ;                 // true
$installment->reminders[ 0 ]->adjustments[ 0 ]->amount->value === 40 ;   // true
```

Les relances existent aussi au niveau de l'échéancier entier (`PaymentSchedule::$reminders`), pas seulement par échéance.

Un `DeliveryNote` peut détailler, ligne par ligne, ce qui a réellement été livré par rapport à ce qui était commandé (livraison partielle, reliquat), et porter la confirmation de réception :

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\business\documents\DeliveryNote;
use xyz\oihana\schema\business\documents\ProofOfDelivery;

$note = new Reflection()->hydrate
([
    DeliveryNote::LINES =>
    [
        [
            DeliveryLine::POSITION           => 1 ,
            DeliveryLine::ORDERED_QUANTITY   => 100 ,
            DeliveryLine::DELIVERED_QUANTITY => 80 ,
            DeliveryLine::BACKORDER_QUANTITY => 20 ,
            DeliveryLine::BACKORDER_REASON   => 'Out of stock' ,
        ],
    ],
    DeliveryNote::PROOF_OF_DELIVERY =>
    [
        ProofOfDelivery::SIGNATORY => 'Jane Doe' ,
        ProofOfDelivery::DATE      => '2026-01-20' ,
    ],
], DeliveryNote::class);

$note->lines[ 0 ] instanceof DeliveryLine ;             // true
$note->lines[ 0 ]->backorderQuantity === 20 ;           // true
$note->proofOfDelivery instanceof ProofOfDelivery ;     // true
```

---

## Catalogue des classes

| Classe | Étend | Rôle |
|---|---|---|
| <a id="taxdetail"></a>`TaxDetail` | `StructuredValue` | Une taxe (`category`, `rate`, `basisAmount`, `taxAmount`) appliquée à une ligne ou à un document. Ne mélange jamais TVA et contributions environnementales — voir `EcoFeeRule`/`AppliedEcoFee`. |
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | Un ajustement de prix (`type`, `amount` ou `percentage`, `reason`, `includedInBase`), inspiré d'UBL `AllowanceCharge`. Couvre remise, majoration, frais de port, éco-participation, consigne, emballage via la seule propriété `type` (voir `PriceComponentType`). |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | La règle de calcul d'une éco-participation (`category`, `rate`, `validFrom`, `validThrough`) — un concept de catalogue, sans effet monétaire propre. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | La trace d'application d'une `EcoFeeRule` sur une ligne (`rule`, `quantity`, `amount`) — l'effet monétaire réel passe toujours par un `Adjustment` de type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | Le récapitulatif monétaire d'un document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`, plus les totaux dérivés optionnels `allowanceTotal`/`chargeTotal` des ajustements de niveau document, miroirs UBL `AllowanceTotalAmount`/`ChargeTotalAmount`), chaque montant en `MonetaryAmount`. Un objet dédié plutôt qu'une réutilisation de `CompoundPriceSpecification`, dont le rôle Schema.org (cumuler des prix qui s'appliquent en parallèle, ex. électricité + nettoyage) ne correspond pas à un récapitulatif HT/taxes/TTC. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | Une ligne de document (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`) — `taxes` et `adjustments` sont scopés à la ligne, un document peut donc mélanger des lignes à taux de TVA différents. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | Un échéancier de paiement (`installments`, une liste de `PaymentInstallment` ; `reminders`, les relances de niveau échéancier). Chaque échéance porte son propre statut de paiement et ses propres relances, ce qui permet de suivre le règlement échéance par échéance. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | Une échéance (`dueDate`, `amount` ou `percentage`, `paymentStatus`, `reminders`). Le `paymentStatus` réutilise `org\schema\enumerations\status\PaymentStatusType` (payé, dû, en retard…), le pendant à l'échelle de l'échéance du `paymentStatus` de la facture ; `reminders` liste les `PaymentReminder` propres à cette échéance. |
| <a id="paymentreminder"></a>`PaymentReminder` | `StructuredValue` | La trace d'une relance de paiement (`date`, `level` → `PaymentReminderLevel`, `channel` → `PaymentReminderChannel`, `status` → `org\schema\enumerations\status\ActionStatusType`, `amountClaimed`, `adjustments`, `note`). Une trace, pas un moteur : la logique d'envoi et de planification reste côté consommateur. Les frais de retard passent par un `Adjustment` (jamais un champ « pénalité » dédié). Rattachable à une échéance ou à l'échéancier entier. |
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | Le parent commun du cycle devis → commande → facture : `adjustments` (ajustements de niveau document, cf. `Adjustment`), `attachments`, `currency`, `customer`, `documentLines`, `issueDate`, `paymentTerms`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`. Étend `Intangible` plutôt que de réutiliser `org\schema\Order`/`org\schema\Invoice` : un document commercial qualifie une transaction, ce n'est pas une ressource adressable en propre — et cela laisse le miroir Schema.org intact (les consommateurs existants d'`org\schema\Order`/`Invoice` ne voient aucun changement). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | Un devis — ajoute `validThrough` (réutilisation de la propriété Schema.org déjà portée par `PriceSpecification`/`Offer`, plutôt qu'un nouveau nom). À ne pas confondre avec `org\schema\creativeWork\Quotation`, qui est une **citation littéraire** sans rapport. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | Un bon de commande — l'engagement confirmé du client, typiquement après acceptation d'un `Quote` : `referencesQuote` (→ un ou plusieurs `Quote`), le maillon amont du cycle et la donnée derrière le statut `BusinessDocumentStatus::CONVERTED`. |
| <a id="invoice"></a>`Invoice` | `BusinessDocument` | Une facture — le document final du cycle devis → commande → facture : `accountId`, `billingPeriod`, `broker`, `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus` (→ `org\schema\enumerations\status\PaymentStatusType`, réutilisant ses classes membres existantes `PaymentComplete`/`PaymentDue`/`PaymentDeclined`/`PaymentPastDue`/`PaymentAutomaticallyApplied`), `provider`, `referencesOrder` (→ un ou plusieurs `PurchaseOrder`, propres à ce namespace), `scheduledPaymentDate`. Reprend les noms de propriétés de `org\schema\Invoice`, mais ne partage volontairement pas de trait de propriétés avec lui : `referencesOrder` doit pointer vers le `PurchaseOrder` maison (pas `org\schema\Order`), et certaines unions du miroir (`broker`, `category`, `billingPeriod`) datent d'avant la convention `null\|array\|X` — les élargir pour un trait commun reviendrait à modifier le miroir, ce que cette hiérarchie s'interdit (voir [`BusinessDocument`](#businessdocument)). |
| <a id="creditnote"></a>`CreditNote` | `BusinessDocument` | Un avoir — corrige ou annule tout ou partie d'une `Invoice` déjà émise : `reason` (justification libre, même nom/type que `Adjustment::$reason`), `referencesInvoice` (→ une ou plusieurs `Invoice`). Le montant corrigé passe par le `totals` hérité (recap positif) ; c'est le type de document (`CreditNote`) qui porte le sens « ça réduit ce qui est dû », pas une convention de signe. |
| <a id="deliverynote"></a>`DeliveryNote` | `BusinessDocument` | Un bon de livraison — atteste la livraison physique des biens d'un `PurchaseOrder` : `orderDelivery` (→ `org\schema\ParcelDelivery`, réutilisant le nom de propriété et le type déjà portés par `org\schema\Order`, plutôt que de réinventer le suivi de colis), `lines` (une liste de `DeliveryLine`, le détail ligne par ligne), `proofOfDelivery` (→ `ProofOfDelivery`). |
| <a id="deliveryline"></a>`DeliveryLine` | `StructuredValue` | Une ligne de `DeliveryNote` : `position` (référence à la ligne d'origine du bon de commande), `item`, `orderedQuantity`/`deliveredQuantity`/`backorderQuantity` (+ `backorderReason`), `batchNumber`/`serialNumbers` (traçabilité optionnelle). Comble le manque confirmé par UBL (`DespatchLine`), GS1/EDIFACT, Odoo et SAP : sans elle, un bon de livraison ne peut dire qu'« un colis est parti », pas combien de quoi a réellement été livré — une lacune dès qu'une livraison est partielle. |
| <a id="proofofdelivery"></a>`ProofOfDelivery` | `StructuredValue` | La confirmation de réception d'une livraison : `signatory`, `date`, `discrepancyNote`. Une trace, pas un moteur (même logique que `PaymentReminder`) : la capture de signature et la résolution des litiges restent côté consommateur. |
| <a id="receipt"></a>`Receipt` | `BusinessDocument` | Un reçu — preuve que le paiement d'une `Invoice` a été reçu : `confirmationNumber`, `paymentMethod`/`paymentMethodId` (repris de `org\schema\Invoice`), `referencesInvoice` (→ une ou plusieurs `Invoice`). Le montant reçu n'est pas dupliqué ici (déjà porté par `totals` hérité) ; la date de réception est le `issueDate` hérité. |
| <a id="statement"></a>`Statement` | `BusinessDocument` | Un relevé de compte — récapitule sur une période les documents qui ont fait bouger le solde d'un compte : `billingPeriod` (repris du nom déjà utilisé par `org\schema\Invoice`), `entries` (une liste de `StatementEntry`), `openingBalance`/`closingBalance` (`MonetaryAmount`, sans équivalent Schema.org — UBL les nomme `BeginningBalanceAmount`/`EndingBalanceAmount`), `totalDebit`/`totalCredit` (agrégats de la période, miroirs UBL `TotalDebitAmount`/`TotalCreditAmount`), `agingSummary` (→ `AgingSummary`, la balance âgée). Seule classe du lot qui n'est pas un simple sous-type à une propriété : elle introduit son propre concept de ligne. |
| <a id="statemententry"></a>`StatementEntry` | `StructuredValue` | Une ligne de `Statement` : `document` (le `BusinessDocument` concerné, ou une simple chaîne si l'objet complet n'est pas disponible), `type` (→ `StatementEntryType` : facture, paiement, avoir…, plutôt que de le déduire du document référencé), `date`, `dueDate` (l'échéance dont se sert la balance âgée), `amount` (le mouvement signé), `debitAmount`/`creditAmount` (ventilation débit/crédit optionnelle, en complément d'`amount`), `balance` (solde courant après cette ligne). Distincte de `BusinessDocumentLine`, qui tarife un produit/service et non un mouvement de compte. |
| <a id="agingsummary"></a>`AgingSummary` | `StructuredValue` | La balance âgée d'un `Statement` : `current`, `days1To30`, `days31To60`, `days61To90`, `over90` (chacun un `MonetaryAmount`). Une convention de reporting attendue d'un relevé de compte (QuickBooks, Xero), qu'UBL lui-même ne porte pas. La bibliothèque ne modélise que la forme : le consommateur calcule chaque tranche (typiquement depuis le `dueDate` de chaque ligne), cet objet stocke le résultat — une valeur, pas un moteur de calcul (même logique que `PaymentReminder`). |
| <a id="businessdocumentexporter"></a>`BusinessDocumentExporter` | *(interface)* | Le contrat de sérialisation d'un `BusinessDocument` : `export(BusinessDocument $document): string`. Les formats réglementaires (UBL, Factur-X, Peppol…) restent hors périmètre pour l'instant. |
| <a id="jsonldexporter"></a>`JsonLdExporter` | `BusinessDocumentExporter` | Implémentation de démonstration : délègue à `ThingTrait::jsonSerialize()` (héritée via `Intangible`/`Thing`) puis `json_encode()`. |

---

## Constantes associées

Chaque classe expose ses constantes de propriétés via un trait dédié sous [`constants/traits/business/documents/`](../../src/xyz/oihana/schema/constants/traits/business/documents), agrégés dans [`DocumentsTrait`](../../src/xyz/oihana/schema/constants/traits/business/DocumentsTrait.php), lui-même composé dans [`BusinessTrait`](../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php) puis dans l'agrégateur global [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) — contrairement à `BusinessIdentityTrait`/`UserProfileTrait`, aucune collision de nom n'a été détectée, donc ces constantes sont directement accessibles via `Oihana::RATE`, `Oihana::AMOUNT`, etc., en plus des constantes de classe (`TaxDetail::RATE`, `Adjustment::AMOUNT`…).

---

## Pour aller plus loin

- [`xyz\oihana\schema\business`](business.md) — `BusinessIdentity`, `UserProfile`.
- [`xyz\oihana\schema\products`](products.md) — `PriceComponentType`, réutilisée par `Adjustment::$type`.
- [`xyz\oihana\schema\enumerations`](../../src/xyz/oihana/schema/enumerations) — `PaymentReminderLevel`/`PaymentReminderChannel`, réutilisées par `PaymentReminder` ; `StatementEntryType`, réutilisée par `StatementEntry`.
- [`org\schema`](../schema-org/README.md) — `MonetaryAmount`, `PriceSpecification`, `StructuredValue`.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
