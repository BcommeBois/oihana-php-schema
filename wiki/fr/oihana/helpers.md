# Fonctions d'assistance — hydratation et pivots

La bibliothèque expose une couche de **fonctions d'assistance** (*helpers*) : des fonctions libres, chargées automatiquement par Composer (section `autoload.files`), qui accompagnent les classes de schéma sans leur ajouter de méthodes. Une fonction d'assistance se consomme par un simple `use function` — pas d'instance, pas d'état, une entrée, une sortie.

Deux familles vivent dans cette couche :

- les **hydrateurs** — transformer une donnée brute (un tableau associatif sorti d'une base ou d'une API) en objet de schéma typé, références imbriquées comprises ;
- les **pivots de compte** — résoudre les identités métier d'un compte authentifié (`User`) en clés d'organisation ou de vendeur, celles qui délimitent le périmètre de ses ressources.

> 🇬🇧 This page is also available in [English](../../en/oihana/helpers.md).

---

## Quand l'utiliser

Choisissez un hydrateur lorsqu'une donnée arrive **déjà structurée mais non typée** — un document projeté par une requête, une réponse JSON décodée — et que vous voulez la manipuler comme un objet de schéma sans écrire le câblage des références imbriquées à la main :

- `hydrateCustomer( $document )` rend un `Customer` dont les `contactPoint` sont des `ContactPoint` et l'`address` une `PostalAddress` ;
- chaque hydrateur accepte indifféremment **une définition simple**, **une liste indexée** de définitions (les entrées invalides sont filtrées), ou **toute autre valeur**, rendue telle quelle (passage à travers).

Choisissez un pivot lorsqu'un compte authentifié doit être ramené à **la clé qui délimite son périmètre** : le client pour lequel travaille un contact, le ou les vendeurs qu'incarne un commercial.

---

## Le chargement

Les fonctions sont enregistrées dans la section `autoload.files` du `composer.json` de la bibliothèque : elles sont disponibles partout, sans instanciation. L'import suit le namespace de la fonction :

```php
use function org\schema\helpers\hydrate\hydratePostalAddress;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;
```

---

## L'étagement des namespaces

La couche respecte la règle de la bibliothèque : `org\schema` est le miroir pur du vocabulaire Schema.org, `xyz\oihana\schema` est l'extension maison qui s'appuie dessus — **jamais l'inverse**.

| Namespace                                        | Contenu                                                                    | Dépend de              |
|--------------------------------------------------|----------------------------------------------------------------------------|------------------------|
| `org\schema\helpers\hydrate`                     | Les 9 hydrateurs Schema.org purs, plus `findEnumerationMember`              | `org\schema` seulement |
| `xyz\oihana\schema\helpers\hydrate`              | Les 10 hydrateurs métier, plus `findPhysicalQuantityByType` et `termClassOf` | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\hydrate\appointments` | Les 4 hydrateurs de rendez-vous                                             | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\hydrate\documents`    | Les 5 hydrateurs de documents                                               | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\pivots`               | Les 4 pivots de compte                                                      | `xyz` + `org`          |

Les hydrateurs métier appellent les hydrateurs purs pour leurs références imbriquées (`hydrateCustomer` délègue à `hydrateContactPoint` et `hydratePostalAddress`) — le sens de la flèche est toujours `xyz` → `org`.

Le sous-dossier `hydrate/documents` regroupe les hydrateurs des [documents commerciaux](business-documents.md). Ils se distinguent des autres sur un point : au lieu d'appeler le constructeur puis de recâbler chaque référence imbriquée à la main, ils passent par `Reflection::hydrate()` — le seul chemin qui honore les attributs `#[HydrateAs]` / `#[HydrateWith]` déjà portés par `BusinessDocumentLine`/`BusinessDocument`. La correspondance reste donc déclarée une seule fois, sur la classe.

`hydrateOrganizationOrPerson`, lui, vit dans `org\schema\helpers\hydrate` : n'ayant besoin que des classes `org\schema\Organization`/`org\schema\Person`, il reste un hydrateur pur. Il résout une union `Organization|Person` d'après le `@type` du contenu, et accepte deux classes cibles personnalisées (`$organizationClass`/`$personClass`) pour viser un sous-type métier.

`hydrateParcelDelivery` est le contre-exemple qui montre le mieux à quoi sert cet étagement. La livraison d'un document est une `org\schema\ParcelDelivery`, mais son mode et sa tournée sont des termes de thésaurus **maison** (`DeliveryMethodTerm`, `DeliveryRouteTerm`). Les nommer dans un `#[HydrateAs]` posé sur `ParcelDelivery` aurait retourné la flèche : la classe Schema.org se serait mise à dépendre de la couche métier. L'hydrateur les prend donc en **paramètres** (`class-string<DefinedTerm>`, avec les termes maison par défaut), et vit du côté `xyz` où cette connaissance a le droit d'exister. C'est aussi pourquoi il est le seul de la famille documentaire à passer par le constructeur plutôt que par `Reflection::hydrate()` : sans attribut à honorer sur ces trois propriétés, la réflexion n'apporterait rien — et sa sévérité écarterait au passage tout ce qu'une livraison stockée porte au-delà du vocabulaire Schema.org.

**Ce que `hydrateParcelDelivery` a inauguré est devenu un motif.** Les hydrateurs de rendez-vous prennent le même genre de paramètre : `hydrateVisitReport` et `hydrateCustomerAppointment` acceptent un `$termClass` qui dit dans quelle classe leurs vocabulaires sont relus — `ThesaurusTerm` par défaut, la classe que les familles maison servent réellement. Sans lui, un terme perdait en silence tout ce que `DefinedTerm` ne déclare pas, `color` en tête : un constructeur n'assigne que les propriétés déclarées par sa classe, et la clé tombait sans erreur ni trace. Le même terme changeait alors d'apparence selon la porte par laquelle on le lisait — sa propre famille, ou le compte rendu qui le cite.

Là où la livraison nomme deux paramètres (`$deliveryMethodClass`, `$deliveryRouteClass`), une rencontre porte quatre vocabulaires et son compte rendu quatre autres. D'où **un seul paramètre à deux formes**, lu par `termClassOf` :

```php
hydrateCustomerAppointment( $raw ) ;                      // le terme maison, partout
hydrateCustomerAppointment( $raw , DefinedTerm::class ) ; // une classe nommée, partout

hydrateCustomerAppointment( $raw ,
[
    Prop::DEFAULT                         => ThesaurusTerm::class ,
    CustomerAppointment::APPOINTMENT_TYPE => AppointmentTypeTerm::class ,
    CustomerAppointment::REPORT           => [ VisitReport::MOOD => MoodTerm::class ] ,
]) ;
```

Les deux formes sont la même phrase à deux niveaux de détail : on n'écrit une carte que le jour où une famille cesse de répondre ce que les autres répondent.

⚠️ **Une carte est indexée par propriété, pas par famille.** `tags` est déclarée sur la rencontre **et** sur son compte rendu, au-dessus de deux familles différentes — les mentions rapides d'une rencontre ne sont pas les qualificatifs du texte écrit à son sujet. Une seule clé `tags` ne peut pas les distinguer : la branche `report` est ce qui les sépare. Sans elle, le compte rendu **hérite de la carte de la rencontre**, ce qui est juste tant que les deux familles s'accordent, et ce que la branche est là pour défaire le jour où elles divergent.

> **Note.** Les unions ambiguës des documents (`customer`/`seller`/`author`, `broker`/`provider`, `item`, `ownedBy`) sont désormais tranchées **déclarativement**, par un `#[HydrateWith(A::class, B::class)]` posé sur la propriété : `Reflection::hydrate()` choisit alors la bonne classe d'après le discriminateur (`@type`, `atType` ou `type`) et, à défaut, d'après les propriétés présentes. La résolution est donc correcte **même sans passer par un helper**. Les hydrateurs de cette couche restent utiles pour ce que la réflexion ne fait pas : accepter indifféremment une définition simple, une liste indexée ou une valeur quelconque rendue telle quelle.

---

## Exemple express — hydrater un client

```php
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;

$customer = hydrateCustomer
([
    'name'         => 'South Wood Company' ,
    'contactPoint' => [ [ 'telephone' => '05 59 00 00 00' ] ] ,
    'address'      => [ 'streetAddress' => '20 Rue Mably' , 'postalCode' => '33000' ] ,
]);

$customer->name ;                        // 'South Wood Company'
$customer->contactPoint[0]->telephone ;  // '05 59 00 00 00'  (ContactPoint)
$customer->address->streetAddress ;      // '20 Rue Mably'    (PostalAddress)
```

Les trois formes acceptées par les hydrateurs de choses (`hydrateCustomer`, `hydrateWarehouse`, `hydrateDefinedTerm`, …) :

```php
hydrateCustomer( [ 'name' => 'A' ] ) ;                      // une définition  → Customer
hydrateCustomer( [ [ 'name' => 'A' ] , [ 'name' => 'B' ] ] ); // une liste       → Customer[]
hydrateCustomer( 'brut' ) ;                                 // autre valeur    → rendue telle quelle
hydrateCustomer( [ 'brut' , [ 'name' => 'A' ] ] ) ;          // liste mixte     → [ 'brut' , Customer ]
```

### La règle des références imbriquées

Un hydrateur qui résout des références imbriquées (`hydrateCustomer`, `hydrateCustomerSite`,
`hydrateBusinessDocument`, …) applique à chacune la même règle :

- **il n'hydrate que ce qui est un tableau** — là où il y a quelque chose à hydrater. Une
  référence textuelle non résolue ou une instance déjà typée est laissée intacte, jamais
  réécrite. 🔑 **Cela vaut entrée par entrée à l'intérieur d'une liste** : une liste de
  poignées — des codes publiés, des identifiants de vocabulaire — ressort telle quelle, et
  les clés restent sans trou, un tableau troué se sérialisant en **objet** JSON ;
- **la réponse de l'hydrateur imbriqué est ensuite écrite telle quelle, `null` compris.**
  Un tableau qui ne résout rien — liste vide, liste d'entrées **qui étaient des tableaux**
  et n'ont rien donné — devient `null`, jamais un tableau brut résiduel ;
- **une propriété que la charge utile n'a jamais portée n'est pas inventée.** L'hydratation
  la laisse dans l'état que lui donne sa déclaration : déclarée sans valeur par défaut,
  elle reste non initialisée, donc absente de la forme sérialisée ; déclarée `= null`, elle
  reste à `null` et est sérialisée comme telle. Dans les deux cas, l'hydratation n'y touche
  pas.

C'est le deuxième point qui met les deux chemins d'accord : une référence imbriquée répond
la même chose à travers son parent qu'à travers l'hydrateur imbriqué appelé seul.

```php
hydrateDeliveryRouteAssignment( [] ) ;                                // null

$site = hydrateCustomerSite( [ 'name' => 'A' , 'deliveryRoute' => [] ] ) ;
$site->deliveryRoute ;                                                // null — la même réponse
```

#### L'exception : les lignes et les ajustements d'un document

Deux hydrateurs échappent à ce `null` — mais **sur leur argument de premier niveau seulement**, jamais sur les références qu'ils résolvent en dessous. `hydrateDocumentLine()` et `hydrateAdjustment()` rendent une **liste vide telle quelle** :

```php
hydrateDocumentLine( [] ) ;   // []  — « ce document n'a aucune ligne »
hydrateAdjustment  ( [] ) ;   // []  — « ce document n'a aucun ajustement »

hydrateDocumentLine( [ null ] ) ;   // null — rien n'était lisible : ce n'est pas la même réponse
hydrateDocumentLine( [ 'brut' ] ) ; // [ 'brut' ] — une poignée n'est pas une ligne illisible, c'est une référence
```

Les lignes et les ajustements sont les deux endroits où « il n'y en a pas » est une réponse qui vaut la peine d'être servie : un brouillon naît couramment sans une seule ligne, et un `null` fait disparaître la clé de la forme sérialisée — le consommateur qui parcourt la valeur tombe alors sur une absence au lieu de la liste vide qu'il pouvait parcourir.

C'est aussi ce qui **remet les deux chemins d'accord** ici : par la réflexion, un `documentLines` vide reste `[]` (l'attribut `#[HydrateWith]` parcourt une liste vide et rend une liste vide). Avant, l'hydrateur appelé seul répondait `null` là où le parent répondait `[]`.

Aucun autre hydrateur ne change : une liste vide passée à `hydrateCustomer`, `hydrateCustomerSite`, `hydrateDeliveryRouteAssignment` — ou trouvée dans une référence imbriquée, quelle qu'elle soit — répond toujours `null`.

---

## Exemple express — hydrater les lignes d'un document

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLine;

// $document : un BusinessDocument sorti du serveur — ses lignes sont des tableaux bruts.

$document->documentLines = hydrateDocumentLine( $document->documentLines ) ;

$line = $document->documentLines[0] ;

$line->quantity->value ;                // 5       (QuantitativeValue)
$line->price->price ;                   // 22.5    (CompoundPriceSpecification)
$line->price->priceComponent[0]->name ; // 'base'  (UnitPriceSpecification)
$line->taxes[0]->rate ;                 // 20.0    (TaxDetail)
$line->total->value ;                   // 135.0   (MonetaryAmount)
$line->item->name ;                     // 'Peinture blanche' (Product)
```

Le constructeur de `BusinessDocument` ne fait qu'une affectation superficielle : sans cet appel, `documentLines` reste un tableau de tableaux, et le prix, la quantité, les taxes et les totaux d'une ligne ne deviennent jamais des objets.

L'`item` d'une ligne est une union `Product|Service` : c'est le `@type` du contenu qui décide de sa classe — `Service` pour une prestation, le `Product` enrichi commerce sinon — grâce au `#[HydrateWith(Product::class, Service::class)]` porté par la propriété.

---

## Exemple express — hydrater le reste de l'en-tête

Le même besoin se pose, slot par slot, quand le document a été construit ailleurs : ses montants, ses ajustements et sa livraison sont alors des tableaux bruts.

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateAdjustment;
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentTotals;
use function xyz\oihana\schema\helpers\hydrate\hydrateParcelDelivery;

$document->totals        = hydrateDocumentTotals( $document->totals        ) ;
$document->adjustments   = hydrateAdjustment    ( $document->adjustments   ) ;
$document->orderDelivery = hydrateParcelDelivery( $document->orderDelivery ) ;

$document->totals->total->value ;                          // 62.4  (MonetaryAmount)
$document->adjustments[0]->amount->value ;                 // 52.0  (MonetaryAmount)
$document->adjustments[0]->taxes[0]->taxAmount->value ;    // 10.4  (MonetaryAmount)
$document->orderDelivery->deliveryAddress->postalCode ;    // '33270' (PostalAddress)
$document->orderDelivery->hasDeliveryMethod->id ;          // 'F13'   (DeliveryMethodTerm)
```

Les deux premiers passent par `Reflection::hydrate()` : `DocumentTotals`, `Adjustment` et `TaxDetail` déclarent tous leurs montants par un `#[HydrateAs(MonetaryAmount::class)]`, et la réflexion **descend** — un seul appel sur un ajustement type donc son `amount`, ses `taxes`, et le `basisAmount`/`taxAmount` que chaque `TaxDetail` déclare à son tour. Rien n'a eu à être ajouté pour `TaxDetail` ni pour `MonetaryAmount` : les attributs étaient déjà là, seul manquait le chemin qui les lit.

Le troisième prend l'autre voie, pour la raison d'étagement dite [plus haut](#létagement-des-namespaces).

---

## Exemple express — hydrater un document entier

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;

// $raw : la réponse brute du serveur pour une facture.

$invoice = hydrateBusinessDocument( $raw , Invoice::class ) ;

$invoice->customer->name ; // 'Jean Dupont' — Person, même si Organization vient en premier dans l'union
$invoice->seller->name   ; // 'ACME'        — Organization
$invoice->provider->name ; // 'Sous-traitant SA' — Organization, même sans @type explicite
```

`customer`, `seller` et `author` sont typés `Organization|Person` sur tout `BusinessDocument` (`broker`/`provider` en plus sur `Invoice`) — la même ambiguïté que l'`item` d'une ligne, transposée à l'en-tête, et tranchée de la même façon par un `#[HydrateWith(Organization::class, Person::class)]`. `hydrateBusinessDocument()` hydrate le document via `Reflection::hydrate()` — donc tout, en-tête comme lignes, sort typé — puis repasse sur ces propriétés via `hydrateOrganizationOrPerson()`. Son second paramètre (`$class`, par défaut `BusinessDocument::class`) sert n'importe quel maillon du cycle — `Quote::class`, `PurchaseOrder::class`, `CreditNote::class`… — puisqu'aucun d'eux ne redéfinit `customer`/`seller`/`author`.

L'intérêt du helper n'est donc pas la résolution de l'union — l'attribut s'en charge, même pour un appel direct à `Reflection::hydrate()` — mais les trois formes d'entrée acceptées : un document, une liste de documents, ou toute autre valeur rendue telle quelle.

---

## Exemple express — les pivots de compte

```php
use function xyz\oihana\schema\helpers\pivots\customerKey;
use function xyz\oihana\schema\helpers\pivots\customerKeys;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;

// $user : un xyz\oihana\schema\auth\User dont les `identities` sont hydratées.

$key     = customerKey( $user )  ; // '137285125' — le client pour lequel le contact travaille, ou null
$clients = customerKeys( $user ) ; // [ '137285125' , '137285130' ] — tous ses clients, dédupliqués
$keys    = sellerKeys( $user )   ; // [ '147737218' , '147737209' ] — les casquettes vendeur, dédupliquées
```

Un compte porte zéro, une ou plusieurs identités métier (voir [`BusinessIdentity`](business.md)) : `customerKey()` et `sellerKey()` résolvent la première du type attendu ; `customerKeys()` et `sellerKeys()` les résolvent toutes.

---

## Catalogue des fonctions

### `org\schema\helpers\hydrate` — les hydrateurs purs

| Fonction                    | Produit                          | Formes acceptées                        |
|-----------------------------|----------------------------------|-----------------------------------------|
| `findEnumerationMember`     | la classe membre d'une énumération | Ne construit rien : **lit** dans une charge brute l'`additionalType` (l'URI que chaque membre déclare), puis à défaut le `@type` (le nom court de la classe), et rend la classe à hydrater — la tête de l'énumération quand rien n'est reconnu, pour qu'un statut inconnu garde le motif qu'il portait. Partagée par les deux aides de statut, les deux vocabulaires posant la même question. |
| `hydrateAdditionalProperty` | `PropertyValue[]`                | liste indexée seulement, sinon `null`   |
| `hydrateContactPoint`       | `ContactPoint[]`                 | liste indexée seulement, sinon `null`   |
| `hydrateDefinedTerm`        | `DefinedTerm` ou `DefinedTerm[]` | simple, liste, passage à travers — classe cible personnalisable via `$class` (ex. `DeliveryMethodTerm::class`) |
| `hydrateEventStatus`        | la classe membre d'`EventStatusType` | simple, liste, passage à travers — **la constante nue ressort intacte**, un tableau redevient l'objet qui porte son motif (`EventCancelled` et sa `description`). Membre résolu par `findEnumerationMember`. |
| `hydrateGeoCoordinates`     | `GeoCoordinates` ou liste        | simple, liste, passage à travers        |
| `hydrateOffer`              | `Offer` **nue**, ou liste        | simple, liste, passage à travers — `eligibleQuantity` et `priceSpecification` viennent de `Reflection::hydrate()`, `itemOffered` est tranché d'après le `@type` de la charge (suffixe `Service` → `Service`, sinon `$productClass`). 🔑 `$productClass` est ce qui garde l'aide dans `org\schema` : le `Product` enrichi commerce vit dans `xyz`, il se passe donc en paramètre. Sert `Organization::$makesOffer` et toute propriété portant une liste d'offres nues. |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | tableau ou instance, sinon `null` — type le `eligibleCustomerType` en `BusinessEntityType` |
| `hydrateOrganizationOrPerson` | `Organization` ou `Person`, ou liste | Résout l'union d'après le `@type` : `Person` → `Person`, sinon `Organization` (défaut sûr) — classes cibles personnalisables via `$organizationClass`/`$personClass` |
| `hydratePostalAddress`      | `PostalAddress` ou liste         | simple (valeurs vides nettoyées), liste, passage à travers |

### `xyz\oihana\schema\helpers\hydrate` — les hydrateurs métier

| Fonction                  | Produit             | Références imbriquées hydratées                          |
|---------------------------|---------------------|----------------------------------------------------------|
| `findPhysicalQuantityByType` | `PhysicalQuantity` ou `null` | Ne construit rien d'imbriqué : **parcourt** une chaîne de conditionnement — passée en paramètre, typée ou brute — et rend l'étage dont l'`additionalType` correspond, poids et volume compris — **sa descendance typée elle aussi**, pour qu'un poids se lise `->weight` à toute profondeur. Le tas de l'arbre est un paramètre parce qu'il n'est pas toujours joignable depuis le produit qui l'a défini : il est recopié sur les offres, et c'est cette copie qui est stockée. Un `valueReference` qui n'est pas un étage — un code nu, une énumération, ce que Schema.org autorise — arrête le parcours. `Product::findEligibleQuantityByType()` n'est plus que cet appel sur son propre arbre. |
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity` (par `hydratePhysicalQuantity`), `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` ou liste | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` ou liste | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` ou liste | `additionalProperty`, `address`, `geo`, `deliveryMethod` (DeliveryMethodTerm), `deliveryRoute` (DeliveryRouteAssignment[]) |
| `hydrateDeliveryRouteAssignment` | `DeliveryRouteAssignment` ou liste | `route` (DeliveryRouteTerm, lorsque la ligne de référence jointe est présente — un code nu est laissé tel quel) |
| `hydrateFeeSpecification` | `FeeSpecification`  | `rate` (UnitPriceSpecification — un barème déjà typé est laissé tel quel plutôt que reconstruit), `publisher` (résolu par `hydrateOrganizationOrPerson`). Accepte aussi bien une instance qu'un tableau, et la complète sur place. Réservé au chemin du **constructeur** : `Reflection::hydrate()` type les deux tout seul, l'attribut étant déclaré sur `FeeSpecification::$rate`. |
| `hydrateParcelDelivery`   | `ParcelDelivery` ou liste | `deliveryAddress` et `originAddress` (PostalAddress), `hasDeliveryMethod` et `hasDeliveryRoute` (classes cibles personnalisables via `$deliveryMethodClass`/`$deliveryRouteClass`, par défaut `DeliveryMethodTerm`/`DeliveryRouteTerm`), `provider` (résolu par `hydrateOrganizationOrPerson`) |
| `hydratePhysicalQuantity` | `PhysicalQuantity`  | `valueReference` — **récursivement**, chaque étage du conditionnement gardant son `weight` et son `volume`. Un niveau déjà typé est rendu tel quel. Réservé au chemin du **constructeur** : `Reflection::hydrate()` descend la chaîne tout seul, l'attribut étant déclaré sur la propriété. |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` ou liste | `ownedBy` (Subsidiary)                                  |
| `termClassOf`             | une `class-string<DefinedTerm>` | Ne construit rien : **répond la classe** dans laquelle une propriété de terme est relue. Lit les deux formes du paramètre `$termClass` — un nom de classe, ou une carte `[ Prop::DEFAULT => …, '<propriété>' => … ]`. Ce qu'une carte ne nomme pas retombe sur `Prop::DEFAULT`, puis sur `ThesaurusTerm`. 🔑 Une **branche imbriquée** — une sous-carte, comme `report` — n'est pas un nom de classe et n'est jamais rendue comme telle : elle appartient à l'hydrateur qui possède cette propriété, qui la lit et la transmet lui-même. |

### `xyz\oihana\schema\helpers\hydrate\appointments` — les hydrateurs de rendez-vous

| Fonction                    | Produit                          | Références imbriquées hydratées                          |
|-----------------------------|-----------------------------------|----------------------------------------------------------|
| `hydrateAppointmentStatus` | la classe membre d'`AppointmentStatus` | Ne descend nulle part : **la constante nue ressort intacte**, un tableau redevient l'objet qui porte son motif. Le jumeau de `hydrateEventStatus` sur l'autre axe — ce qu'il est advenu de la rencontre, non du créneau. ⚠️ Le vocabulaire écrit `…/AppointmentStatus#NoShow` là où la classe s'appelle `AppointmentNoShow` : aucune règle ne mène de l'un à l'autre, l'URI est donc **déclaré** par le membre. |
| `hydrateCustomerAppointment` | `CustomerAppointment` ou liste | `customer`, `attendee` (CustomerEmployee[] et leurs propres références), `assignedSeller` (Seller), `appointmentType` (un terme) et `tags` (plusieurs) — **relus en `ThesaurusTerm`**, classe cible personnalisable via `$termClass` (un nom de classe, ou une carte par propriété lue par `termClassOf`, branche `report` comprise) —, `makesOffer` (par `hydrateOffer`, avec le `Product` maison), `report` (par `hydrateVisitReport`), `eventStatus` et `appointmentStatus`. Le reste vient de `Reflection::hydrate()`. 🔑 `organizer`, `assignedCompany` et `location` sont **laissés à leur attribut**, que la réflexion tranche déjà exactement d'après le `@type` : leur imposer une classe relirait une organisation simple en filiale, une salle virtuelle en site client. |
| `hydrateFollowUp`         | `FollowUp` ou liste              | `followUpType`, `agent` (résolu par `hydrateOrganizationOrPerson`), `result` — 🚨 **à plat, jamais l'aide profonde** : la rencontre nommée en résultat est une référence, et descendre ouvrirait un cycle que seule la donnée arrête. **Une liste vide est rendue telle quelle.** |
| `hydrateVisitReport`      | `VisitReport` ou liste           | `attendee` (CustomerEmployee[]), `followUp` (FollowUp[], liste vide conservée), `mood` et `outcome` (un terme), `tags` et `topics` (plusieurs) — les quatre **relus en `ThesaurusTerm`**, classe cible personnalisable via `$termClass` (un nom de classe, ou une carte par propriété lue par `termClassOf`) —, `author` (résolu par `hydrateOrganizationOrPerson`) |

### `xyz\oihana\schema\helpers\hydrate\documents` — les hydrateurs de documents

| Fonction                    | Produit                          | Références imbriquées hydratées                          |
|-----------------------------|-----------------------------------|----------------------------------------------------------|
| `hydrateAdjustment`       | `Adjustment` ou liste            | `amount` (MonetaryAmount), `taxes` (TaxDetail[], avec leurs propres `basisAmount`/`taxAmount`) — **une liste vide est rendue telle quelle** |
| `hydrateBusinessDocument` | `BusinessDocument` (ou sous-classe via `$class`), ou liste | `customer`, `seller`, `author` (et `broker`/`provider` sur `Invoice`), résolus par `hydrateOrganizationOrPerson` — le reste (`documentLines`, `taxes`, `totals`…) vient de `Reflection::hydrate()` |
| `hydrateDocumentLine`     | `BusinessDocumentLine` ou liste  | `adjustments` (Adjustment[]), `price` (CompoundPriceSpecification + son `priceComponent`), `quantity`, `subtotal`, `taxes` (TaxDetail[]), `total`, `item` (délégué ci-dessous) — **une liste vide est rendue telle quelle** |
| `hydrateDocumentLineItem` | `Product` ou `Service`, ou liste | Résout l'union d'après le `@type` : suffixe `Service` → `Service`, sinon `Product` (avec ses `eligibleQuantity` et `inventoryLevel`) |
| `hydrateDocumentTotals`   | `DocumentTotals` ou liste        | Les sept montants déclarés (`allowanceTotal`, `balanceDue`, `chargeTotal`, `prepaidAmount`, `subtotal`, `total`, `totalTax`) en `MonetaryAmount` — une liste vide répond `null`, des totaux absents se disant par une valeur absente |

### `xyz\oihana\schema\helpers\pivots` — les pivots de compte

| Fonction      | Rend                | Rôle                                                                 |
|---------------|---------------------|----------------------------------------------------------------------|
| `customerKey` | `_key` ou `null`    | L'organisation cliente pour laquelle travaille le premier contact du compte (`worksFor`). |
| `customerKeys`| liste de `_key`     | Toutes les organisations clientes dont le compte est contact, dédupliquées, jamais de `null`. |
| `sellerKey`   | `_key` ou `null`    | La clé de la première identité vendeur du compte.                    |
| `sellerKeys`  | liste de `_key`     | Toutes les clés vendeur du compte, dédupliquées, jamais de `null`.   |

---

## Voir aussi

- [Métier Oihana](business.md) — `BusinessIdentity`, le lien compte ↔ entité que les pivots parcourent.
- [Documents commerciaux](business-documents.md) — `BusinessDocument`, `BusinessDocumentLine` et les objets de valeur que les hydrateurs de documents produisent.
- [Rendez-vous](appointments.md) — `CustomerAppointment`, `VisitReport`, `FollowUp` et la section « relire un rendez-vous ».
- [Vocabulaire Schema.org](../schema-org/README.md) — les classes produites par les hydrateurs purs.
