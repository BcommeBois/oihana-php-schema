# `xyz\oihana\schema\statistics` — Statistiques Oihana

Le namespace `xyz\oihana\schema\statistics` décrit un **corps de chiffres** : ce que quelqu'un — un client, un fournisseur — a échangé sur une période, mesuré sous plusieurs angles à la fois.

> 🇬🇧 This page is also available in [English](../../en/oihana/statistics.md).

---

## Quand l'utiliser

Choisissez ces classes lorsque vous devez **stocker, servir ou relire des chiffres déjà mesurés** — un chiffre d'affaires annuel et son détail mensuel, une marge, des volumes — plutôt que les documents qui les ont produits :

- restituer la performance d'un client ou d'un fournisseur année après année,
- alimenter un tableau de bord commercial (cumuls, tendances, comparaisons),
- conserver ce qu'un progiciel de gestion a calculé de son côté, sans le recalculer.

Les fiches étendent `org\schema\Intangible` : une lecture chiffrée n'est ni un document, ni un fichier publié. Elles exposent le distinguisheur `@context = 'https://schema.oihana.xyz'` dans le JSON-LD.

> ℹ️ **Pourquoi pas `Dataset` ?** Un `Dataset` (Schema.org) est un **corpus de données publié** : il se catalogue, se télécharge, porte un éditeur et une licence. Une fiche de statistiques est une **lecture** — un sujet, une période — bien plus proche de l'`Observation` dont elle est faite que du corpus dans lequel elle finira peut-être.

---

## Exemple express

```php
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\statistics\CustomerStatistics;
use xyz\oihana\schema\statistics\ObservationSeries;

$statistics = new CustomerStatistics
([
    CustomerStatistics::ABOUT              => '369980' ,          // le sujet, par sa référence
    CustomerStatistics::DIRECTION          => BusinessDocumentDirection::SALE ,
    CustomerStatistics::YEAR               => 2025 ,
    CustomerStatistics::OBSERVATION_PERIOD => 'P1M' ,             // douze valeurs par série
    CustomerStatistics::ASSIGNED_COMPANY   => '501' ,
    CustomerStatistics::ASSIGNED_SELLER    => 'JDOE' ,

    CustomerStatistics::REVENUE => new ObservationSeries
    ([
        Oihana::UNIT_CODE => 'EUR' ,
        Oihana::VALUE     => 271465.89 ,               // le total de l'année
        Oihana::VALUES    => [ 31545.48 , 32030.05 , 25604.00 , 37633.43 , 28802.74 , 24753.51 ,
                             23417.21 ,  7824.02 , 13665.69 , 17116.81 , 17232.02 , 11840.93 ] ,
    ]),

    CustomerStatistics::GROSS_MARGIN => new ObservationSeries
    ([
        Oihana::UNIT_CODE => 'EUR' ,
        Oihana::VALUE     => 66631.13 ,                // pas de détail mensuel : la source n'en publie pas
    ]),
]);

echo json_encode( $statistics , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Et la relecture d'un document stocké, qui retype tout l'arbre d'un coup :

```php
use oihana\reflect\Reflection;

$statistics = new Reflection()->hydrate( $document , CustomerStatistics::class );

$statistics->about;           // xyz\oihana\schema\organizations\Customer
$statistics->assignedSeller;  // org\schema\Person
$statistics->revenue;         // xyz\oihana\schema\statistics\ObservationSeries
$statistics->revenue->values[ 0 ] ;  // janvier
```

---

## Le modèle en trois pièces

| Pièce                                      | Ce qu'elle dit                                                                                                                                           |
|--------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|
| **La fiche** (`Statistics`)                | *de qui* on parle, de *quel côté* de l'échange, sur *quelle année*, à *quel pas* les séries sont découpées, et pour *quelle société*. **Aucun chiffre.** |
| **La mesure** (`ObservationSeries`)        | *un* indicateur, lu sur toute la période (`value`) et pas à pas (`values`), dans son unité (`unitCode`).                                                 |
| **Les dix mesures** (`HasTradingMeasures`) | le jeu d'indicateurs du négoce, déclaré **une seule fois** et composé par chaque famille.                                                                |

Une **famille** est une fiche dont le sujet est nommé : `CustomerStatistics` pour un client, `ProviderStatistics` pour un fournisseur. Elle ajoute ses propres dimensions et rien d'autre — le reste est hérité ou composé.

```
                       Statistics  (Intangible)
                       about · direction · year · observationPeriod · assignedCompany
                                    ▲
                 ┌──────────────────┴──────────────────┐
        CustomerStatistics                     ProviderStatistics
        + assignedSeller · assignedPOS         (aucune dimension propre)

        toutes deux composent  HasTradingMeasures  → dix ObservationSeries
```

---

## Catalogue des classes

| Classe               | Étend                    | Rôle                                                                                          |
|----------------------|--------------------------|-----------------------------------------------------------------------------------------------|
| `Statistics`         | `Intangible`             | La tête d'une fiche : sujet, direction, année, pas des séries, société. Aucune mesure.        |
| `ObservationSeries`  | `org\schema\Observation` | Une mesure, lue sur toute la période et pas à pas.                                            |
| `CustomerStatistics` | `Statistics`             | Ce qu'un **client** a échangé sur une année, plus le commercial et le point de vente d'alors. |
| `ProviderStatistics` | `Statistics`             | Ce qui a été acheté à un **fournisseur** sur une année.                                       |
| `SellerStatistics`   | `Statistics`             | Ce qu'un **commercial** a échangé sur une année, éventuellement client par client.            |
| `SalesObjectives`    | `Statistics`             | Ce qu'un **commercial** vise sur une année — les mêmes mesures, lues comme des cibles.        |

### Propriétés de `Statistics`

| Propriété           | Type                                      | Description                                                                  |
|---------------------|-------------------------------------------|------------------------------------------------------------------------------|
| `about`             | `int\|string\|array\|Thing\|null`         | Le sujet : la référence brute lue de la source (un code), ou l'objet résolu. |
| `direction`         | `string\|BusinessDocumentDirection\|null` | De quel côté de l'échange les chiffres ont été mesurés — vente ou achat.     |
| `year`              | `int\|null`                               | L'année civile couverte.                                                     |
| `observationPeriod` | `string\|null`                            | Le pas des séries, en durée ISO 8601 — `P1M` pour une valeur par mois.       |
| `assignedCompany`   | `int\|string\|array\|Organization\|null`  | La société pour laquelle les chiffres ont été mesurés.                       |

`id`, `name`, `url`, `additionalType`, `created` et `modified` sont hérités de `Thing`.

### Propriétés d'`ObservationSeries`

| Propriété  | Type                           | Description                                                                                      |
|------------|--------------------------------|--------------------------------------------------------------------------------------------------|
| `values`   | `array<int, int\|float>\|null` | La mesure, une valeur par pas de la période. Absente si la source ne publie qu'un total.         |
| `value`    | `mixed`                        | Le total sur la période. *(hérité de `QuantitativeValue`)* Absent si la source ne le publie pas. |
| `unitCode` | `string\|null`                 | L'unité — `MTQ`, `KGM`, ou le code ISO 4217 d'une mesure monétaire (`EUR`). *(hérité)*           |

`observationAbout`, `observationDate`, `marginOfError`, `measuredProperty` et les autres termes d'`Observation` restent disponibles ; la fiche les portant déjà pour toutes ses mesures, on ne les répète pas sur chacune.

### Les dix mesures — `HasTradingMeasures`

| Mesure           | Ce qu'elle mesure                                                | Total | Série | Confidentielle |
|------------------|------------------------------------------------------------------|:-----:|:-----:|:--------------:|
| `revenue`        | Ce que l'échange a rapporté — le montant facturé.                | ✅    | ✅    |                |
| `purchaseCost`   | L'échange valorisé au **prix payé** pour la marchandise.         | —     | ✅    | 🔒             |
| `averageCost`    | L'échange valorisé au **prix moyen pondéré** du stock.           | —     | ✅    | 🔒             |
| `costPrice`      | L'échange valorisé au **prix de revient** (achat + frais).       | —     | ✅    | 🔒             |
| `purchaseMargin` | La marge au-dessus du prix d'achat.                              | ✅    | —     | 🔒             |
| `averageMargin`  | La marge au-dessus du prix moyen pondéré.                        | ✅    | —     | 🔒             |
| `grossMargin`    | La marge au-dessus du prix de revient — la **marge brute**.      | ✅    | —     | 🔒             |
| `quantity`       | Les quantités échangées, dans l'unité de comptage des articles.  | —     | ✅    |                |
| `volume`         | Le volume occupé par la marchandise.                             | —     | ✅    |                |
| `weight`         | Le poids de la marchandise.                                      | —     | ✅    |                |

*(les colonnes « Total » et « Série » indiquent ce qu'une source publie **d'ordinaire** — voir la règle ci-dessous)*

### Propriétés de `CustomerStatistics`

| Propriété        | Type                                  | Description                                           |
|------------------|---------------------------------------|-------------------------------------------------------|
| `about`          | `#[HydrateAs(Customer::class)]`       | Le client — même union que sur la fiche, sujet nommé. |
| `assignedSeller` | `int\|string\|array\|Person\|null`    | Le commercial auquel le client était rattaché.        |
| `assignedPOS`    | `int\|string\|array\|Warehouse\|null` | Le point de vente qui servait le client.              |

`ProviderStatistics` nomme son sujet un `Provider` et n'ajoute aucune dimension : un fournisseur n'est pas rattaché à un commercial ni à un point de vente comme l'est un client.

### Propriétés de `SellerStatistics` et de `SalesObjectives`

| Propriété          | Type                                            | Description                                                                 |
|--------------------|-------------------------------------------------|-----------------------------------------------------------------------------|
| `about`            | `#[HydrateAs(Seller::class)]`                   | Le commercial — même union que sur la fiche, sujet nommé.                   |
| `assignedCustomer` | `int\|string\|array\|Customer\|null`           | Le client sur lequel porte le chiffre ou la cible. Absent si la source totalise le commercial. |
| `assignedCategory` | `array\|string\|CategoryCode\|Thing\|null`     | *(`SalesObjectives` seul)* Le rayon de marchandises visé — un code, ou les codes ordonnés d'un chemin de classification, du plus large au plus fin. Absent si la cible porte sur un client. |

**Les deux narrations sont exclusives** : une cible porte sur un client **ou** sur un rayon, jamais sur les deux, et une cible posée sur le seul commercial les laisse toutes deux absentes.

**Les deux classes portent le même sujet, et c'est tout l'intérêt.** Le réalisé et la cible s'alignent clé pour clé, sans rien à traduire de l'un vers l'autre.

⚠️ **Une cible est rarement aussi détaillée qu'elle en a l'air.** Il est courant qu'une source ne renseigne qu'une seule mesure — un chiffre d'affaires — et laisse les neuf autres vides ; et quand une cible annuelle porte bien une valeur par mois, ce détail est souvent l'annuel étalé sur une courbe de saison plutôt que douze décisions. Rien de tout cela ne se voit dans la fiche une fois écrite : le lecteur qui a besoin de le savoir doit l'apprendre de qui l'a publiée.

⚠️ **Attribuer un chiffre à un commercial est un choix, et deux choix défendables se contredisent.** Une source qui attribue au moment de la vente crédite celui qui l'a faite, définitivement. Un portefeuille lu depuis `CustomerStatistics::$assignedSeller` crédite celui qui tient le compte *aujourd'hui*, et déplace tout un historique avec le compte. Les deux énoncés sont vrais, ils ne parlent pas de la même chose, et ils divergent à chaque transfert.

---

## 🔑 La règle : une mesure porte ce que sa source affirme

Un total sans détail mensuel et un détail mensuel sans total sont **deux cas ordinaires** : une marge publiée une fois l'an n'a pas de détail à donner, un coût publié mois par mois n'a jamais été totalisé.

**Aucune de ces absences n'est comblée par la bibliothèque.** Une fois écrits, un total calculé par nos soins et une valeur mensuelle déduite ressemblent trait pour trait à des valeurs publiées : le lecteur n'a plus aucun moyen de faire la différence. Sommer, diviser, transformer une marge en taux — tout cela appartient à qui affiche les chiffres, et y reste visible.

En pratique, dans un document servi :

- `grossMargin` porte un `value` et **pas** de `values` ;
- `purchaseCost` porte des `values` et **pas** de `value` ;
- une mesure qu'une source ne publie pas **n'apparaît pas du tout** — plutôt qu'à zéro, qui affirmerait que rien n'a été échangé.

## 🔒 Six mesures sur dix sont confidentielles par nature

Les trois coûts et les trois marges disent ce qu'un opérateur gagne sur un partenaire donné. Ce sont exactement les chiffres qu'un client ne doit **jamais** lire à son propre sujet.

⚠️ **Les cacher de la projection ne suffit pas.** Trier sur une marge, filtrer par tranches successives, dénombrer une facette ou regrouper par un coût reconstitue une valeur aussi sûrement que la lire. La bibliothèque décrit la forme ; **la garde appartient à l'application consommatrice**, seule à connaître ses lecteurs — et elle doit couvrir toutes ces surfaces à la fois.

---

## Un document complet

```json
{
  "@type": "CustomerStatistics",
  "@context": "https://schema.oihana.xyz",
  "id": "369980-sale-2025-501",
  "about": { "@type": "Customer", "id": "369980", "name": "Charpentes du Nord" },
  "direction": "https://schema.oihana.xyz/BusinessDocumentDirection#Sale",
  "year": 2025,
  "observationPeriod": "P1M",
  "assignedCompany": { "@type": "Subsidiary", "id": "501", "name": "Bois & Panneaux" },
  "assignedSeller": { "@type": "Person", "id": "JDOE", "name": "Jane Doe" },
  "assignedPOS": { "@type": "Warehouse", "id": "1", "name": "Dépôt nord" },
  "revenue": {
    "@type": "ObservationSeries", "unitCode": "EUR", "value": 271465.89,
    "values": [31545.48, 32030.05, 25604.00, 37633.43, 28802.74, 24753.51, 23417.21, 7824.02, 13665.69, 17116.81, 17232.02, 11840.93]
  },
  "costPrice": {
    "@type": "ObservationSeries", "unitCode": "EUR",
    "values": [24029.83, 23829.30, 18954.56, 27516.63, 21754.45, 18365.76, 17665.70, 6629.80, 10555.01, 13512.72, 12744.33, 9276.67]
  },
  "grossMargin": { "@type": "ObservationSeries", "unitCode": "EUR", "value": 66631.13 },
  "weight": {
    "@type": "ObservationSeries", "unitCode": "KGM",
    "values": [15561.29, 11705.11, 12023.16, 13984.03, 19368.78, 34424.62, 11401.22, 3875.47, 8373.95, 22811.65, 11937.06, 8512.40]
  },
  "modified": "2026-08-19T03:12:40+02:00"
}
```

---

## Constantes associées

Les clés de propriétés sont exposées par les traits [`StatisticsRecordTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/StatisticsRecordTrait.php), [`ObservationSeriesTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/ObservationSeriesTrait.php), [`HasTradingMeasuresTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/HasTradingMeasuresTrait.php) [`CustomerStatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/CustomerStatisticsTrait.php), [`SellerStatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/SellerStatisticsTrait.php) et [`SalesObjectivesTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/SalesObjectivesTrait.php), composés dans l'agrégateur de domaine [`StatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/StatisticsTrait.php) et câblés dans la classe maîtresse [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php). Vous pouvez donc y accéder via `Oihana::YEAR`, `Oihana::REVENUE`, `Oihana::GROSS_MARGIN`, etc. — et chaque classe expose les siennes (`CustomerStatistics::REVENUE`).

---

## Voir aussi

- [Vocabulaire Schema.org](../schema-org/README.md) — `Observation`, `QuantitativeValue` et `StatisticalVariable`, le socle sur lequel une mesure repose.
- [Documents commerciaux](business-documents.md) — les documents dont ces chiffres sont issus, et l'énumération `BusinessDocumentDirection` qu'ils partagent.
- [Entités commerciales](organizations.md) — `Customer` et `Provider`, les sujets de ces deux familles.
