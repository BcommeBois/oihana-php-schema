# `xyz\oihana\schema\business\documents` — Documents commerciaux

Le namespace `xyz\oihana\schema\business\documents` modélise le cycle **devis → bon de commande → facture** (et ses à-côtés : avoir, bon de livraison, reçu, relevé). Il réutilise le vocabulaire Schema.org partout où il existe (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) et s'inspire d'UBL 2.3 pour les concepts absents de Schema.org (ajustements de prix, ventilation de taxes, échéancier de paiement) — sans recopier les XSD UBL.

> 🇬🇧 This page is also available in [English](../../en/oihana/business-documents.md).

---

## État de ce namespace

Cette page documente l'intégralité du namespace : les **objets de valeur transverses** (`TaxDetail`, `Adjustment`, `PaymentReminder`, `DeliveryLine`, `ProofOfDelivery`, `AgingSummary`, `GoodsReceiptLine`…), la **hiérarchie de documents** complète (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`, `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`, `Statement`) et l'**export** (`BusinessDocumentExporter`, `JsonLdExporter`).

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
| Représenter une note de débit (l'inverse d'un avoir, augmente ce qui est dû). | [`DebitNote`](#debitnote) |
| Représenter un bon de livraison. | [`DeliveryNote`](#deliverynote) |
| Détailler, ligne par ligne, ce qui a réellement été livré (par rapport à ce qui était commandé). | [`DeliveryLine`](#deliveryline) |
| Consigner la confirmation de réception d'une livraison (signataire, date, écart constaté). | [`ProofOfDelivery`](#proofofdelivery) |
| Confirmer côté acheteur la réception des marchandises (quantités reçues, écarts). | [`GoodsReceiptConfirmation`](#goodsreceiptconfirmation) / [`GoodsReceiptLine`](#goodsreceiptline) |
| Représenter un reçu de paiement (côté vendeur). | [`Receipt`](#receipt) |
| Représenter un avis de paiement (côté payeur). | [`RemittanceAdvice`](#remittanceadvice) |
| Représenter un relevé de compte périodique. | [`Statement`](#statement) / [`StatementEntry`](#statemententry) |
| Répartir un solde client par ancienneté de retard (balance âgée). | [`AgingSummary`](#agingsummary) |
| Sérialiser un document commercial (JSON-LD, et demain UBL/Factur-X…). | [`BusinessDocumentExporter`](#businessdocumentexporter) / [`JsonLdExporter`](#jsonldexporter) |

Les objets de valeur (`TaxDetail`, `Adjustment`…, ainsi que `StatementEntry`, `GoodsReceiptLine`) étendent `org\schema\StructuredValue` (comme `MonetaryAmount` ou `PriceSpecification`) : ce sont des valeurs structurées, pas des ressources adressables. `BusinessDocument` et ses déclinaisons (`Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`, `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`, `Statement`) étendent `org\schema\Intangible` — voir [`BusinessDocument`](#businessdocument) pour la justification de cet ancrage. Toutes exposent le distinguisheur `@context = 'https://schema.oihana.xyz'`.

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

Ce même besoin se pose isolément quand seul le tableau `documentLines` est disponible — par exemple une réponse serveur où le `BusinessDocument` a déjà été construit ailleurs et n'a pas traversé `Reflection::hydrate()`. Le helper [`hydrateDocumentLine()`](helpers.md#xyzoihanaschemahelpershydratedocuments--les-hydrateurs-de-documents) couvre ce cas précis, lignes brutes ou déjà hydratées, simple ligne ou liste.

Trois autres emplacements d'en-tête se retrouvent dans la même situation, et ont chacun leur helper : [`hydrateDocumentTotals()`](helpers.md#xyzoihanaschemahelpershydratedocuments--les-hydrateurs-de-documents) pour le récapitulatif, [`hydrateAdjustment()`](helpers.md#xyzoihanaschemahelpershydratedocuments--les-hydrateurs-de-documents) pour les ajustements de niveau document, et [`hydrateParcelDelivery()`](helpers.md#xyzoihanaschemahelpershydrate--les-hydrateurs-métier) pour la livraison. Les deux premiers passent par la réflexion, qui **descend** : un seul appel sur un ajustement type son `amount`, ses `taxes`, et le `basisAmount`/`taxAmount` que chaque `TaxDetail` déclare à son tour.

🔑 Les lignes et les ajustements sont les deux seuls emplacements où une **liste vide est rendue telle quelle** plutôt que ramenée à `null` : un brouillon naît couramment sans une seule ligne, et « il n'y en a pas » est une réponse qui vaut la peine d'être servie.

**Les unions de parties.** `customer`, `seller` et `author` (et, sur `Invoice`, `broker`/`provider`) sont typés `Organization|Person` — une union qu'un type de propriété ne suffit pas à trancher, la réflexion retenant sinon le premier membre déclaré quel que soit le contenu. Chacune porte donc un `#[HydrateWith(Organization::class, Person::class)]` : `Reflection::hydrate()` choisit alors la classe d'après le discriminateur du payload (`@type`, `atType` ou `type`) et, à défaut, d'après les propriétés présentes. Même mécanisme pour l'`item` d'une `BusinessDocumentLine`, d'une `DeliveryLine` ou d'une `GoodsReceiptLine` (`Product|Service`). Un identifiant brut (chaîne ou entier) passé à la place d'un objet reste inchangé.

Le helper [`hydrateBusinessDocument()`](helpers.md#xyzoihanaschemahelpershydratedocuments--les-hydrateurs-de-documents) n'est donc pas nécessaire à cette résolution : il ajoute les formes d'entrée (document seul, liste de documents, passage à travers) et sert n'importe quel maillon du cycle via son second paramètre (`hydrateBusinessDocument( $raw , Quote::class )`, `Invoice::class`…).

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

**Ligne vs document.** Un `Adjustment` s'applique soit à une ligne (`BusinessDocumentLine::$adjustments` — remise propre à un article), soit au document entier (`BusinessDocument::$adjustments` — remise de pied de document, forfait de port ou emballage facturés globalement), sur le modèle d'UBL `AllowanceCharge`. L'effet cumulé des ajustements de niveau document se relit, si besoin, dans les champs dérivés optionnels `DocumentTotals::$allowanceTotal` (total des remises) et `DocumentTotals::$chargeTotal` (total des majorations/frais), miroirs des `AllowanceTotalAmount`/`ChargeTotalAmount` d'UBL. Ces champs dérivés disent un montant total, jamais son origine : la taxe qu'un ajustement doit se lit sur l'ajustement lui-même (`Adjustment::$taxes`), à la forme des taxes de ligne.

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
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | Un ajustement de prix (`type`, `amount` ou `percentage`, `reason`, `includedInBase`, `taxes`), inspiré d'UBL `AllowanceCharge`. Couvre remise, majoration, frais de port, éco-participation, consigne, emballage via la seule propriété `type` (voir `PriceComponentType`). Un ajustement à effet monétaire peut porter la taxe qu'il doit en propre, à un taux qui lui est propre : la TVA d'un port répond au transporteur, pas à la marchandise livrée. |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | La règle de calcul d'une éco-participation (`category`, `rate`, `validFrom`, `validThrough`) — un concept de catalogue, sans effet monétaire propre. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | La trace d'application d'une `EcoFeeRule` sur une ligne (`rule`, `quantity`, `amount`) — l'effet monétaire réel passe toujours par un `Adjustment` de type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | Le récapitulatif monétaire d'un document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`, plus les totaux dérivés optionnels `allowanceTotal`/`chargeTotal` des ajustements de niveau document, miroirs UBL `AllowanceTotalAmount`/`ChargeTotalAmount`), chaque montant en `MonetaryAmount`. Un objet dédié plutôt qu'une réutilisation de `CompoundPriceSpecification`, dont le rôle Schema.org (cumuler des prix qui s'appliquent en parallèle, ex. électricité + nettoyage) ne correspond pas à un récapitulatif HT/taxes/TTC. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | Une ligne de document (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`, `additionalProperty`, `color`, `freeReason`, `includedInTotal`, `section`, `technicalNote`) — `taxes` et `adjustments` sont scopés à la ligne, un document peut donc mélanger des lignes à taux de TVA différents. `additionalProperty` (paires `PropertyValue`, hydratées par `Reflection::hydrate()`) porte les caractéristiques hors vocabulaire — numéro de lot, numéro de série, indicateur métier propre à l'ERP. `color` (composée depuis `HasColor`, la même indication maison `#RRGGBB` que portent les familles du thésaurus et `ProductType`) permet de teinter la ligne pour sa propre raison, sans remonter à l'item puis à son type. `freeReason` (→ `DefinedTerm`) dit pourquoi la marchandise part sans être facturée — geste commercial, casse, échantillon, marchandise déjà au client : **c'est sa présence qui dit que la ligne est offerte**, aucun booléen ne l'accompagne, un drapeau et un motif tenus en parallèle finissant toujours par diverger. Le terme est figé par son code et son libellé (`id` + `name`), jamais par sa clé de stockage, qu'une réharvest du vocabulaire renumérote. `technicalNote` est le pendant de la `description` héritée : celle-ci s'adresse au client, celle-là à qui prépare la marchandise (« reprendre les 3 colis palette 12, ne pas remettre en stock ») — les garder séparées est ce qui permet d'imprimer un même document deux fois, pour deux publics. Elle se sérialise comme toute autre propriété : c'est au rendu client de l'omettre, le type ne le fait pas à sa place. `includedInTotal` dit si la ligne compte dans les totaux du document — **son absence veut dire qu'elle compte**, seule une ligne écartée le déclare, à `false`. Une ligne chiffrée n'est pas toujours une ligne à payer : un devis peut proposer deux fois le même ouvrage — deux parquets, deux finitions — et attendre que le client en retienne un ; les deux sont imprimés, les deux sont chiffrés, un seul est facturé. Les lignes de l'option écartée sont des lignes ordinaires, avec un article, une quantité et un prix : rien dans leur contenu ne les distingue, et les additionner malgré tout a été mesuré comme doublant un devis réel. La même case sert à tout ce qui s'affiche sans être dû — un frais indicatif, un montant cité pour mémoire ; à ne pas lire comme « cette ligne est une variante », la raison de l'écart ne la regarde pas, seul le fait compte. `section` porte l'intitulé du chapitre auquel la ligne appartient — « charpente », « parquet chêne », « pose » — un simple libellé répété sur chaque ligne du même groupe, et volontairement rien de plus : ni classe de section, ni imbrication, ni sous-total propre. Le regroupement est ce qui partage le libellé, ce qui suffit à imprimer un document en chapitres et coûte assez peu pour valoir d'être porté même quand rien ne le lit encore. 🚨 **Ne jamais y ranger le sous-total du groupe** : un récapitulatif posé à côté des lignes qu'il récapitule est le plus court chemin pour compter deux fois la même marchandise — il est dérivé, et il le reste. À distinguer de la `description` héritée, qui nomme l'article : une description appartient à une ligne, un intitulé est partagé par plusieurs. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | Un échéancier de paiement (`installments`, une liste de `PaymentInstallment` ; `reminders`, les relances de niveau échéancier). Chaque échéance porte son propre statut de paiement et ses propres relances, ce qui permet de suivre le règlement échéance par échéance. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | Une échéance (`dueDate`, `amount` ou `percentage`, `paymentStatus`, `reminders`). Le `paymentStatus` réutilise `org\schema\enumerations\status\PaymentStatusType` (payé, dû, en retard…), le pendant à l'échelle de l'échéance du `paymentStatus` de la facture ; `reminders` liste les `PaymentReminder` propres à cette échéance. |
| <a id="paymentreminder"></a>`PaymentReminder` | `StructuredValue` | La trace d'une relance de paiement (`date`, `level` → `PaymentReminderLevel`, `channel` → `PaymentReminderChannel`, `status` → `org\schema\enumerations\status\ActionStatusType`, `amountClaimed`, `adjustments`, `note`). Une trace, pas un moteur : la logique d'envoi et de planification reste côté consommateur. Les frais de retard passent par un `Adjustment` (jamais un champ « pénalité » dédié). Rattachable à une échéance ou à l'échéancier entier. |
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | Le parent commun du cycle devis → commande → facture : `adjustments` (ajustements de niveau document, cf. `Adjustment`), `assignedSeller`, `attachments`, `author`, `authority` (→ `BusinessDocumentAuthority`), `billingAddress`, `contact`, `currency`, `customer`, `direction` (→ `BusinessDocumentDirection`), `documentLines`, `issueDate`, `orderDelivery`, `paymentTerms`, `pointOfSale`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`, `weight`. Les quatre propriétés d'en-tête `billingAddress` (`PostalAddress` — l'adresse de facturation), `contact` (`Person` — l'interlocuteur, absent du vocabulaire Schema.org), `orderDelivery` (`ParcelDelivery` — l'adresse de livraison, le mode, la tournée et la date souhaitée) et `pointOfSale` (`Place` — le point de vente qui porte le document) complètent les parties (`customer`/`seller`) et se recopient figées, pour que le document se suffise à lui-même même si les fiches évoluent ensuite. S'y ajoutent `direction` (→ `BusinessDocumentDirection` : la direction commerciale du document — vente ou achat — du point de vue de l'opérateur, indiquant laquelle des parties `seller`/`customer` est sa propre organisation ; orthogonale au type du document et à son cycle `BusinessDocumentStatus`) et `author` (`Organization`/`Person` — la partie qui a rédigé le document, réutilisant le nom Schema.org `author`). `assignedSeller` (`Person`, ou sa référence brute) nomme le **commercial** à qui le document est affecté, à ne pas confondre avec `seller`, la partie qui l'**émet** : une même organisation émet tous les documents d'un cycle, seul `assignedSeller` peut donc répondre à « de qui sont ces devis ? » — la question par laquelle `sellerKey()` cadre les ressources d'un commercial, exactement comme `Customer::$assignedSeller` le fait côté client. Le document en reçoit couramment la valeur depuis la fiche client, puis la garde : réaffecter un client l'an prochain ne doit pas réécrire qui a pris la commande de cette année. Aucun `#[HydrateAs]` n'est déclaré et aucun n'est nécessaire : l'union ne nomme qu'une classe, `Reflection::hydrate()` résout donc seul une ligne jointe en `Person` — et chaque entrée d'une liste jointe — tandis qu'une clé nue (chaîne ou entier) reste telle quelle. `authority` (→ `BusinessDocumentAuthority`) dit **quel système fait foi** sur le document : le nôtre, ou celui d'où il a été moissonné. **Son absence veut dire qu'il est à nous** — seul ce qui vient d'ailleurs le déclare, et rien de ce qui a été écrit avant l'arrivée de la propriété ne change de sens. Elle répond à une question qu'aucune autre ne pose : la classe dit ce que le document *est*, `direction` de quel côté de l'échange nous nous tenons, `status` où il en est de son cycle — aucune ne dit **qui a le droit de le modifier**. La distinction ne devient visible que lorsque des documents des deux origines partagent une collection : un devis rédigé ici et une facture recopiée d'un progiciel se ressemblent, mais corriger la seconde est une correction que le prochain rafraîchissement efface sans un mot, et son auteur n'apprendra jamais qu'elle est perdue. ⚠️ Porter le fait n'est pas l'imposer : refuser l'écriture revient à qui expose le document — un schéma porte du sens, pas des permissions. `weight` dit **ce que pèse la marchandise couverte par le document** — le chiffre imprimé sur un bon de livraison ou un devis, et celui sur lequel un transporteur établit son prix. C'est ordinairement la somme, sur les lignes, de la quantité par le poids de l'unité dans laquelle elle est exprimée. Un nombre nu le porte quand l'unité est implicite, un `QuantitativeValue` quand elle est déclarée (`{ value: 326.5456, unitCode: "KGM" }`) ; un tableau s'hydrate en `QuantitativeValue`. L'union est celle d'`OfferShippingDetails::$weight` élargie d'`array`, pour qu'un poids se lise pareil partout où on le rencontre : l'élargissement est ce qui permet à une ligne brute de se poser dans la propriété jusqu'à ce que l'hydratation la remplace, `#[HydrateAs]` n'agissant qu'à travers `Reflection::hydrate()` et jamais par le constructeur. L'union du miroir est antérieure à cette convention, et le miroir reste intact. ⚠️ **Ne pas le ranger dans `totals`** malgré l'attraction du mot : cette classe est le récapitulatif **monétaire** et chacune de ses propriétés est un `MonetaryAmount` — une masse posée au milieu brouillerait ce qu'elle est. La propriété est volontairement neutre entre poids brut et poids net : si la distinction devient nécessaire, elle appartiendra à l'`additionalType` d'un `QuantitativeValue`, jamais à une seconde propriété — deux poids tenus en parallèle finissent par diverger. Étend `Intangible` plutôt que de réutiliser `org\schema\Order`/`org\schema\Invoice` : un document commercial qualifie une transaction, ce n'est pas une ressource adressable en propre — et cela laisse le miroir Schema.org intact (les consommateurs existants d'`org\schema\Order`/`Invoice` ne voient aucun changement). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | Un devis — ajoute `validThrough` (réutilisation de la propriété Schema.org déjà portée par `PriceSpecification`/`Offer`, plutôt qu'un nouveau nom). À ne pas confondre avec `org\schema\creativeWork\Quotation`, qui est une **citation littéraire** sans rapport. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | Un bon de commande — l'engagement confirmé du client, typiquement après acceptation d'un `Quote` : `referencesQuote` (→ un ou plusieurs `Quote`), le maillon amont du cycle et la donnée derrière le statut `BusinessDocumentStatus::CONVERTED`. |
| <a id="invoice"></a>`Invoice` | `BusinessDocument` | Une facture — le document final du cycle devis → commande → facture : `accountId`, `billingPeriod`, `broker`, `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus` (→ `org\schema\enumerations\status\PaymentStatusType`, réutilisant ses classes membres existantes `PaymentComplete`/`PaymentDue`/`PaymentDeclined`/`PaymentPastDue`/`PaymentAutomaticallyApplied`), `provider`, `referencesDeliveryNote` (→ un ou plusieurs `DeliveryNote`), `referencesOrder` (→ un ou plusieurs `PurchaseOrder`, propres à ce namespace), `scheduledPaymentDate`. 🔑 `referencesDeliveryNote` est souvent **le seul chemin vers les lignes de la facture** : une facture de fin de mois se construit sur ce qui est sorti de l'entrepôt pendant la période, et une source qui appose le numéro de facture sur la livraison — et non sur chaque ligne — ne laisse que ce lien pour remonter à ce qui a réellement été facturé. C'est le pendant de `referencesOrder`, non son remplaçant : les commandes disent ce sur quoi on s'est engagé, les livraisons ce qui est parti. Une commande peut être facturée sur deux mois, et une facture solder des livraisons appartenant à plusieurs commandes — aucune des deux références ne décrit cela seule. Reprend les noms de propriétés de `org\schema\Invoice`, mais ne partage volontairement pas de trait de propriétés avec lui : `referencesOrder` doit pointer vers le `PurchaseOrder` maison (pas `org\schema\Order`), et certaines unions du miroir (`broker`, `category`, `billingPeriod`) datent d'avant la convention `null\|array\|X` — les élargir pour un trait commun reviendrait à modifier le miroir, ce que cette hiérarchie s'interdit (voir [`BusinessDocument`](#businessdocument)). |
| <a id="creditnote"></a>`CreditNote` | `BusinessDocument` | Un avoir — corrige ou annule tout ou partie d'une `Invoice` déjà émise : `reason` (justification libre, même nom/type que `Adjustment::$reason`), `reasonCode` (→ `CreditNoteReasonCode`, la cause structurée en plus du texte libre), `referencesInvoice` (→ une ou plusieurs `Invoice`), `remainingBalance` (la part de l'avoir pas encore imputée, cf. `RemainingCredit` Xero / `Balance` QuickBooks), `disposition` (→ `CreditNoteDisposition` : remboursé, ré-appliqué ou en attente). Le montant corrigé passe par le `totals` hérité (recap positif) ; c'est le type de document (`CreditNote`) qui porte le sens « ça réduit ce qui est dû », pas une convention de signe. |
| <a id="debitnote"></a>`DebitNote` | `BusinessDocument` | Une note de débit — l'inverse symétrique de l'avoir : elle **augmente** ce que doit le client, pour corriger une facture sous-évaluée : `reason`, `referencesInvoice` (→ une ou plusieurs `Invoice`). UBL en fait un type de document à part entière ; le montant correctif passe par le `totals` hérité, c'est le type de document qui porte le sens « ça augmente ce qui est dû ». |
| <a id="deliverynote"></a>`DeliveryNote` | `BusinessDocument` | Un bon de livraison — atteste la livraison physique des biens d'un `PurchaseOrder` : `orderDelivery` (→ `org\schema\ParcelDelivery`, réutilisant le nom de propriété et le type déjà portés par `org\schema\Order`, plutôt que de réinventer le suivi de colis — `hasDeliveryRoute` compris, le circuit sur lequel la marchandise a voyagé), `lines` (une liste de `DeliveryLine`, le détail ligne par ligne), `proofOfDelivery` (→ `ProofOfDelivery`), `referencesOrder` (→ un ou plusieurs `PurchaseOrder`). Une livraison n'est pas l'expédition d'une commande : une tournée charge ce qui est prêt pour un client ce jour-là, et ce qui est prêt appartient rarement à une seule commande — un bon porte couramment quelques lignes d'une commande et quelques lignes d'une autre, le reste des deux attendant le départ suivant. Sans `referencesOrder`, le bon était le seul document du cycle incapable de dire à quoi il répond : `referencesQuote` remonte du devis à la commande, `referencesOrder` de la commande à la facture, et la livraison restait hors de cette chaîne. ⚠️ Il nomme les commandes que le bon touche, pas ce qui a été livré de chacune : cela revient à `DeliveryLine`, une ligne à la fois. |
| <a id="deliveryline"></a>`DeliveryLine` | `StructuredValue` | Une ligne de `DeliveryNote` : `position` (référence à la ligne d'origine du bon de commande), `referencesOrder` (la commande dont vient la ligne), `item`, `orderedQuantity`/`deliveredQuantity`/`backorderQuantity` (+ `backorderReason`), `batchNumber`/`serialNumbers` (traçabilité optionnelle). Une ligne de livraison est un fait en propre, ni une copie de la ligne de commande ni un renvoi vers elle : elle dit ce qui est parti **sur ce bon-là**, de cette ligne, ce jour-là. La quantité appartient à la livraison — ce qui explique qu'une ligne puisse partir en deux moitiés, et qu'un document qui se contenterait de pointer vers les lignes de la commande ne saurait pas l'exprimer. 🔑 `referencesOrder` n'est pas un confort de navigation : dès qu'un bon mélange plusieurs commandes, une `position` seule ne nomme plus rien — la ligne 325 de quelle commande ? — et sans elle `orderedQuantity` n'est même pas remplissable lorsque la source ne porte la quantité commandée que sur la commande. Elle garde la référence brute telle que lue, ou la `PurchaseOrder` résolue : l'union ne nommant qu'une classe, `Reflection::hydrate()` résout seul une ligne jointe, tandis qu'une clé nue reste telle quelle. Comble le manque confirmé par UBL (`DespatchLine`), GS1/EDIFACT, Odoo et SAP : sans elle, un bon de livraison ne peut dire qu'« un colis est parti », pas combien de quoi a réellement été livré — une lacune dès qu'une livraison est partielle. |
| <a id="proofofdelivery"></a>`ProofOfDelivery` | `StructuredValue` | La confirmation de réception d'une livraison : `signatory`, `date`, `discrepancyNote`. Une trace, pas un moteur (même logique que `PaymentReminder`) : la capture de signature et la résolution des litiges restent côté consommateur. |
| <a id="goodsreceiptconfirmation"></a>`GoodsReceiptConfirmation` | `BusinessDocument` | Une confirmation de réception de marchandises — c'est l'acheteur qui confirme avoir reçu les biens d'un `DeliveryNote` : `referencesDeliveryNote` (→ un ou plusieurs `DeliveryNote`), `lines` (une liste de `GoodsReceiptLine`). C'est ce que modélise réellement le `ReceiptAdvice` d'UBL/Peppol (à ne pas confondre avec `Receipt`, qui est une preuve de **paiement**). Premier document côté acheteur d'une hiérarchie jusqu'ici côté vendeur. |
| <a id="goodsreceiptline"></a>`GoodsReceiptLine` | `StructuredValue` | Une ligne de `GoodsReceiptConfirmation` : `position`, `item`, `expectedQuantity`/`receivedQuantity`, `condition` (état des biens reçus), `discrepancyNote`. Le pendant acheteur de `DeliveryLine`. |
| <a id="receipt"></a>`Receipt` | `BusinessDocument` | Un reçu — preuve qu'un paiement a été reçu : `confirmationNumber`, `paymentMethod`/`paymentMethodId` (repris de `org\schema\Invoice`), `referencesInvoice` (→ une ou plusieurs `Invoice`, **facultatif**). Le montant reçu n'est pas dupliqué ici (déjà porté par `totals` hérité) ; la date de réception est le `issueDate` hérité. Deux formes : adossé à une ou plusieurs factures (cas courant), ou **vente au comptant sans facture préalable** (`referencesInvoice` laissé nul, la vente est portée directement par les `documentLines`/`taxes`/`totals` hérités — le cas du ticket de caisse, sans type dédié). |
| <a id="remittanceadvice"></a>`RemittanceAdvice` | `BusinessDocument` | Un avis de paiement — le document que le **payeur** envoie au bénéficiaire pour détailler un règlement : `amountRemitted`, `referencesInvoice` (→ une ou plusieurs `Invoice`). Le pendant côté payeur de `Receipt` (côté vendeur) : les deux coexistent volontairement, chacun modélisant le même événement depuis un bout opposé de la transaction. Défini dans UBL sous le nom `RemittanceAdvice`. |
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
- [`xyz\oihana\schema\enumerations`](../../src/xyz/oihana/schema/enumerations) — `BusinessDocumentAuthority`, réutilisée par `BusinessDocument::$authority` ; `PaymentReminderLevel`/`PaymentReminderChannel`, réutilisées par `PaymentReminder` ; `StatementEntryType`, réutilisée par `StatementEntry` ; `CreditNoteReasonCode`/`CreditNoteDisposition`, réutilisées par `CreditNote`.
- [`org\schema`](../schema-org/README.md) — `MonetaryAmount`, `PriceSpecification`, `StructuredValue`.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
