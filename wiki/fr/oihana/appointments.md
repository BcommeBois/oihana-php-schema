# `xyz\oihana\schema\appointments` — Rendez-vous

Le namespace `xyz\oihana\schema\appointments` décrit une **rencontre convenue avec un client** — chez lui, sur un chantier, au téléphone, en visioconférence — et **ce qu'on en écrit** une fois qu'elle a eu lieu.

> 🇬🇧 This page is also available in [English](../../en/oihana/appointments.md).

---

## Quand l'utiliser

Choisissez ces classes lorsque vous devez **planifier, retrouver ou raconter une rencontre** :

- tenir l'agenda d'un commercial — ce qu'il a aujourd'hui, cette semaine, ce mois ;
- préparer une visite : qui on va voir, où, et ce qu'on compte lui montrer ;
- consigner ce qui s'est dit, sur quel ton, et ce qu'il reste à faire ;
- compter — combien de visites, combien ont abouti, lesquelles sont à relancer.

Un rendez-vous étend `org\schema\Event`. C'est ce que **tout agenda lit déjà** : son lieu, ses horaires, les personnes attendues et celle dont c'est l'agenda sont les propriétés de Schema.org, pas des inventions maison. Ce que le vocabulaire n'a pas de mot pour dire s'ajoute à côté — le client, le type de rencontre, ce qu'on veut lui présenter, le compte rendu.

> ℹ️ **Pourquoi pas une `Action` ?** Une `Action` décrit un agent qui agit sur un objet et produit un résultat — et un rendez-vous en a l'air. Mais elle n'a **pas de mot pour dire qu'un créneau a été déplacé**, là où `Event` publie `eventStatus` et `previousStartDate` ; et c'est l'`Event` que les agendas, les exports iCalendar et les composants d'interface savent lire sans qu'on leur apprenne rien. Le vocabulaire de l'action reste employé là où il est juste : la suite à donner en est une.

---

## Exemple express

```php
use org\schema\constants\Schema;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;

$appointment = new CustomerAppointment
([
    Schema::NAME               => 'Revue de gamme' ,
    Schema::START_DATE         => '2026-09-03T09:30:00+02:00' ,
    Schema::END_DATE           => '2026-09-03T10:30:00+02:00' ,
    Schema::DESCRIPTION        => 'Présenter la nouvelle gamme, relancer le devis en cours.' ,

    Oihana::APPOINTMENT_TYPE   => 'VISIT' ,                       // un terme de thésaurus
    Oihana::APPOINTMENT_STATUS => AppointmentStatus::PLANNED ,
    Oihana::TAGS               => [ 'MEAL' , 'DEMO' ] ,        // les mentions rapides

    Schema::ORGANIZER          => [ Schema::ID => 'JDOE' , Schema::NAME => 'Jane Doe' ] ,
    Schema::CUSTOMER           => [ Schema::ID => '100200' , Schema::NAME => 'Acme Corporation' ] ,
    Schema::ATTENDEE           => [ [ Schema::NAME => 'Alice Smith' ] ] ,
]);

echo json_encode( $appointment , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Et la relecture d'un document stocké, qui retype tout l'arbre d'un coup :

```php
use oihana\reflect\Reflection;

$appointment = new Reflection()->hydrate( $document , CustomerAppointment::class );

$appointment->organizer;          // xyz\oihana\schema\auth\User
$appointment->customer;           // xyz\oihana\schema\organizations\Customer
$appointment->attendee[ 0 ];      // xyz\oihana\schema\people\CustomerEmployee
$appointment->location;           // xyz\oihana\schema\places\CustomerSite
$appointment->report;             // xyz\oihana\schema\appointments\VisitReport
$appointment->report->followUp;   // xyz\oihana\schema\appointments\FollowUp[]
```

> ⚠️ **Le constructeur assigne brut, l'hydratation type.** `new CustomerAppointment([ … ])` recopie ce qu'on lui donne tel quel ; c'est `Reflection::hydrate()` qui lit les attributs et rend les objets imbriqués dans leur classe. Les deux chemins sont normaux — le premier écrit, le second relit.

---

## Le modèle en trois pièces

| Pièce | Ce qu'elle dit |
|---|---|
| **La rencontre** (`CustomerAppointment`) | *avec qui*, *quand*, *où*, *de quelle nature*, et *ce qu'on compte présenter*. Écrite **avant**. |
| **Le compte rendu** (`VisitReport`) | *comment ça s'est passé*, *ce que ça a produit*, *qui était vraiment là*. Écrit **après**. |
| **La suite** (`FollowUp`) | *ce qui reste dû*, *pour quand*, et le rendez-vous éventuellement pris pour l'honorer. |

```
             CustomerAppointment  (Event)
                     customer · organizer · location · attendee · appointmentType
                     appointmentStatus · tags · makesOffer
                              │
                              └── report ──▶ VisitReport  (CreativeWork)
                                             mood · outcome · topics · attendee · text
                                                   │
                                                   └── followUp[] ──▶ FollowUp  (ScheduleAction)
                                                                      followUpType · scheduledTime
                                                                      actionStatus · result ──▶ CustomerAppointment
```

Ce qui est écrit **avant** et ce qui est écrit **après** cohabitent : `description`, `makesOffer` et `tags` sont la préparation, `report` est ce qui en est advenu. L'un n'écrase jamais l'autre — une rencontre vaut d'être relue précisément pour l'écart entre les deux.

---

## Catalogue des classes

| Classe | Étend | Rôle |
|---|---|---|
| `CustomerAppointment` | `org\schema\Event` | Une rencontre convenue avec un client. |
| `VisitReport` | `org\schema\CreativeWork` | Ce qui a été écrit une fois la rencontre passée. |
| `FollowUp` | `org\schema\actions\ScheduleAction` | Ce qui reste à faire ensuite, et pour quand. |
| `AppointmentStatus` | `org\schema\enumerations\StatusEnumeration` | Ce qu'il est advenu de la rencontre elle-même. |
| `AppointmentCancelled`, `AppointmentDone`, `AppointmentNoShow`, `AppointmentPlanned` | `AppointmentStatus` | Les classes membres du statut — la forme objet, celle qui porte un motif. |

---

## 🔑 Deux axes d'état, et ils ne se remplacent pas

C'est le point le plus important de la page, et celui qui coûte le plus cher à découvrir en chemin.

| Axe | Propriété | Ce qu'il dit | Valeurs |
|---|---|---|---|
| **Le créneau** | `eventStatus` *(hérité d'`Event`)* | ce qu'il est advenu de **l'horaire** | `EventScheduled`, `EventRescheduled`, `EventPostponed`, `EventCancelled`, `EventMovedOnline` |
| **La rencontre** | `appointmentStatus` | ce qu'il est advenu de **la rencontre** | `Planned`, `Done`, `NoShow`, `Cancelled` |

Schema.org **ne publie aucun membre pour « ça a eu lieu »** : son énumération suit un événement annoncé, pas un événement vécu. Un agenda lit le premier axe — un rendez-vous déplacé garde son `previousStartDate` et reste à venir ; un compte rendu lit le second — une rencontre `Done` ne bouge plus. Un seul axe ne pourrait pas répondre aux deux questions.

🔑 **`NoShow` n'est pas `Cancelled`.** Une annulation est annoncée et libère le temps ; une absence se découvre sur le pas de la porte et coûte le déplacement. Les compter ensemble masque la seule des deux sur laquelle on peut agir.

🔑 **Et un statut peut dire *pourquoi*.** Les deux axes acceptent la constante nue — `EventStatusType::CANCELLED` — ou la **classe membre** quand il y a davantage à dire : `new EventCancelled([ 'description' => 'Le client a annulé la veille.' ])`. Les deux écritures répondent le même URI, donc le choix est libre ; la seconde survit à l'aller-retour en base parce que l'union accepte aussi un tableau.

---

## Propriétés de `CustomerAppointment`

| Propriété | Type | Description |
|---|---|---|
| `appointmentStatus` | `string\|array\|AppointmentStatus\|null` | Ce qu'il est advenu de la rencontre. Se lit à côté d'`eventStatus`. |
| `appointmentType` | `string\|array\|DefinedTerm\|null` | La nature de la rencontre — chez le client, au téléphone, en visio, sur un chantier. Une seule valeur. |
| `assignedCompany` | `string\|array\|Organization\|null` | La **société pour laquelle** la rencontre a été prise — celle de l'organisateur, figée à la création. Relue en `Subsidiary`. Elle est là pour qu'un périmètre (« les rendez-vous de mon agence ») soit **un filtre et non un parcours**. |
| `assignedSeller` | `int\|string\|array\|Person\|null` | Le commercial auquel le client est rattaché. Peut différer de l'organisateur. |
| `attendee` | `Person\|Organization\|array\|null` | Les contacts du client **attendus**. Facultatifs, aucun ou plusieurs. Relus en `CustomerEmployee`. |
| `customer` | `array\|Organization\|Person\|null` | Le client. Une référence et sa copie figée, ou un client **libre** — un nom, un téléphone — pour une entreprise qui n'est pas encore au fichier. Relu en `Customer`. |
| `location` | `PostalAddress\|Place\|VirtualLocation\|string\|array\|null` | Le lieu : une adresse du client, un chantier, une salle virtuelle. Relu en `CustomerSite`, `JobSite`, `Place`… |
| `makesOffer` | `Offer[]\|null` | Ce qu'on compte présenter. Voir ci-dessous. |
| `organizer` | `Person\|Organization\|array\|null` | Le **compte** dont c'est l'agenda. Relu en `User`. 🔑 Le compte, jamais le rôle métier : une personne qui gagne un second rôle garde **un seul agenda**. |
| `report` | `array\|VisitReport\|null` | Le compte rendu. **Un seul**, absent tant qu'il n'y a rien à raconter. |
| `tags` | `string[]\|DefinedTerm[]\|null` | Les mentions rapides — repas avec le client, visite de l'entreprise, chantier, démonstration. Plusieurs. |

`name`, `description`, `startDate`, `endDate`, `duration`, `eventStatus`, `eventAttendanceMode`, `previousStartDate`, `remarks`, `about`, `subEvent`/`superEvent` sont hérités d'`Event` ; `id`, `identifier`, `url`, `created`, `modified` de `Thing`.

### 🔑 Le client est la seule chose dont une rencontre ne peut pas se passer

Tout le reste est facultatif : les contacts attendus, le lieu, ce qu'on compte montrer. Et le client peut prendre deux formes, sans que la classe ait à distinguer :

```php
// Un client connu : une référence et sa copie figée.
Schema::CUSTOMER => [ '@type' => 'Customer' , '_key' => '137191259' , 'id' => '100200' , 'name' => 'Acme Corporation' ]

// Un client libre : ce qu'on sait de lui, et pas de clé.
Schema::CUSTOMER => [ 'name' => 'Acme Corporation' , 'telephone' => '05 56 00 00 00' ]
```

La bibliothèque accepte les deux ; c'est au consommateur de décider quand la référence devient obligatoire, et comment un client libre est **rattaché** plus tard à une fiche créée entre-temps.

### 🔑 `makesOffer` — une intention, pas un devis

Ce qu'un commercial compte mettre sous les yeux de son client s'écrit en **offres**, une par produit :

```php
Schema::MAKES_OFFER =>
[
    [
        Schema::DESCRIPTION  => 'Lui montrer le modèle A plutôt que le B.' ,
        Schema::ITEM_OFFERED => [ 'id' => '500100' , 'name' => 'Article modèle A' ,
                                  'image' => [ '@type' => 'ImageObject' , 'contentUrl' => 'https://example.org/model-a.jpg' ] ] ,
    ],
]
```

L'enveloppe est ce qui porte l'intention **à côté** de la référence — et le jour où l'intention devient un chiffre (une remise envisagée, une quantité qui vaut d'être chiffrée), `Offer` a déjà `price`, `priceSpecification` et `eligibleQuantity` : aucune propriété à inventer. Le nom et le sens sont ceux que `Organization::$makesOffer` porte déjà.

⚠️ **Rien ici n'engage personne.** Le document qui engage s'écrit ailleurs, et pointe vers la rencontre dont il est issu (`Thing::$subjectOf`).

---

## Propriétés de `VisitReport`

| Propriété | Type | Description |
|---|---|---|
| `attendee` | `array\|Person\|null` | Qui était **vraiment** là. Relus en `CustomerEmployee`. |
| `followUp` | `FollowUp[]\|null` | Ce qui reste à faire. Aucune, une ou plusieurs. |
| `mood` | `string\|array\|DefinedTerm\|null` | Le climat de la rencontre — satisfait, neutre, un problème à traiter. Une seule valeur. |
| `outcome` | `string\|array\|DefinedTerm\|null` | Ce que la rencontre a produit — une commande, un devis à écrire, une relance, rien. Une seule valeur. |
| `tags` | `string[]\|DefinedTerm[]\|null` | Des qualificatifs du compte rendu lui-même. Déclarée pour le jour où — les qualificatifs d'une rencontre vivent sur la rencontre. |
| `topics` | `string[]\|DefinedTerm[]\|null` | Les sujets abordés. Plusieurs. |

`text` (le corps du compte rendu), `author`, `dateCreated`, `dateModified`, `audio` et `associatedMedia` sont hérités de `CreativeWork`.

🔑 **Les cases et le texte ne sont pas des alternatives.** Un compte rendu réduit à des codes perd ce qui le rend digne d'être lu ; réduit à de la prose, il ne se compte pas. Les deux sont déclarés, **aucun n'est obligatoire** : celui qu'on écrit sur un téléphone, dans une camionnette, en trois gestes, vaut mieux que le compte rendu exhaustif qui ne sera jamais écrit.

🔑 **Le climat n'est pas le résultat.** Une rencontre peut bien se passer et ne rien produire, une rencontre tendue finir en commande. Lus ensemble, les deux disent quelque chose qu'aucun ne dit seul.

⚠️ **Les présents du compte rendu ne sont pas ceux de la rencontre.** L'un dit qui était attendu, l'autre qui est venu. Ils divergent assez souvent pour qu'on ne les confonde pas : les fusionner réécrirait discrètement un plan en constat.

---

## Propriétés de `FollowUp`

| Propriété | Type | Description |
|---|---|---|
| `followUpType` | `string\|array\|DefinedTerm\|null` | La nature du prochain geste — rappeler, envoyer le devis, repasser. |
| `result` | `Thing\|array\|string\|null` | Le rendez-vous **pris** pour l'honorer, quand il y en a un. Relu en `CustomerAppointment`. |

`scheduledTime` (pour quand c'est dû), `actionStatus` (encore dû, ou honoré), `agent` (qui le doit), `name` et `description` sont hérités de `ScheduleAction` et d'`Action`.

🔑 **Une promesse n'est pas un rendez-vous.** « Le rappeler dans quinze jours » est dû par quelqu'un et n'a pas de créneau. Écrire la promesse comme un rendez-vous mettrait un fantôme dans l'agenda et effacerait la différence entre **ce qui est convenu** et **ce qui est calé**. Le jour où le rendez-vous est pris, il est nommé dans `result` — et la promesse passe en `CompletedActionStatus`.

---

## Les disponibilités d'un commercial

Elles ne vivent pas ici mais sur le **compte** — [`User::$hoursAvailable`](auth.md) — parce qu'elles ne dépendent d'aucune rencontre : c'est le rythme dans lequel les rencontres viennent se poser. Et sur le compte plutôt que sur un rôle métier, parce qu'une personne qui gagne un second rôle garde **un seul agenda** et **un seul jeu d'horaires**.

```php
use xyz\oihana\schema\auth\User;

$user = new User
([
    'name'           => 'Jane Doe' ,
    'hoursAvailable' =>
    [
        [ 'dayOfWeek' => [ 'Monday' , 'Tuesday' , 'Thursday' ] , 'opens' => '08:30' , 'closes' => '18:00' ] ,
        [ 'dayOfWeek' => 'Friday' , 'opens' => '08:30' , 'closes' => '12:00' ] ,
        [ 'validFrom' => '2026-08-10' , 'validThrough' => '2026-08-21' ] ,   // une fermeture : ni opens ni closes
    ],
]);
```

🔑 **Le silence n'est pas une ouverture.** Qui propose un créneau a besoin d'un énoncé **positif** de quand il peut le proposer ; ne rien dire signifie qu'aucun créneau n'est proposable — la lecture prudente plutôt que la permissive.

---

## Le document complet

```json
{
  "@type": "CustomerAppointment",
  "@context": "https://schema.oihana.xyz",
  "name": "Revue de gamme",
  "startDate": "2026-09-03T09:30:00+02:00",
  "endDate": "2026-09-03T10:30:00+02:00",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "appointmentStatus": "https://schema.oihana.xyz/AppointmentStatus#Done",
  "appointmentType": "VISIT",
  "tags": ["MEAL", "DEMO"],
  "organizer": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
  "assignedSeller": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
  "customer": { "@type": "Customer", "id": "100200", "name": "Acme Corporation" },
  "attendee": [ { "@type": "CustomerEmployee", "id": "55", "name": "Alice Smith", "jobTitle": "ACH" } ],
  "location": {
    "@type": "CustomerSite", "name": "Siège social",
    "address": { "@type": "PostalAddress", "streetAddress": "1 Example Street", "postalCode": "10000", "addressLocality": "Exampleville" },
    "geo": { "@type": "GeoCoordinates", "latitude": 44.84, "longitude": -0.64 }
  },
  "description": "Présenter la nouvelle gamme, relancer le devis en cours.",
  "makesOffer": [
    {
      "@type": "Offer",
      "description": "Lui montrer le modèle A plutôt que le B ; remise possible à partir de cent pièces.",
      "itemOffered": { "@type": "Product", "id": "500100", "name": "Article modèle A" }
    }
  ],
  "report": {
    "@type": "VisitReport",
    "text": "Gamme bien reçue. Le client veut un prix pour cent pièces livrées.",
    "outcome": "QUOTE",
    "mood": "GREEN",
    "topics": ["PRICING", "DELIVERY"],
    "attendee": [ { "@type": "CustomerEmployee", "id": "55", "name": "Alice Smith" } ],
    "followUp": [
      {
        "@type": "FollowUp",
        "followUpType": "CALL",
        "scheduledTime": "2026-09-10",
        "description": "Rappeler après envoi du devis.",
        "actionStatus": "https://schema.org/PotentialActionStatus"
      }
    ],
    "author": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
    "dateCreated": "2026-09-03T10:41:00+02:00"
  },
  "created": "2026-08-19T10:02:00+02:00",
  "modified": "2026-09-03T10:41:00+02:00"
}
```

---

## Relire un rendez-vous

Un consommateur relit rarement un rendez-vous par la réflexion : il tient une ligne de base et écrit `new CustomerAppointment( $row )`. Or **un constructeur affecte à plat** — les attributs `#[HydrateAs]` / `#[HydrateWith]` que portent les classes ne sont honorés que par `Reflection::hydrate()`, chemin qu'il ne prend pas. Le `report` reste un tableau, chaque contact aussi, et le statut écrit sous sa forme objet revient en tableau brut.

Quatre aides couvrent le domaine, sur le modèle de la famille `hydrateXxx()` de la bibliothèque :

| Fonction | Rend | Références imbriquées hydratées |
|---|---|---|
| [`hydrateCustomerAppointment()`](helpers.md#xyzoihanaschemahelpershydrateappointments--les-hydrateurs-de-rendez-vous) | `CustomerAppointment` ou liste | `customer`, `attendee`, `assignedSeller`, `appointmentType`, `tags`, `makesOffer`, `report`, `eventStatus`, `appointmentStatus` |
| [`hydrateVisitReport()`](helpers.md#xyzoihanaschemahelpershydrateappointments--les-hydrateurs-de-rendez-vous) | `VisitReport` ou liste | `attendee`, `followUp`, `mood`, `outcome`, `tags`, `topics`, `author` |
| [`hydrateFollowUp()`](helpers.md#xyzoihanaschemahelpershydrateappointments--les-hydrateurs-de-rendez-vous) | `FollowUp` ou liste | `followUpType`, `agent`, `result` |
| [`hydrateAppointmentStatus()`](helpers.md#xyzoihanaschemahelpershydrateappointments--les-hydrateurs-de-rendez-vous) | la classe membre d'`AppointmentStatus` | — |

```php
use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateCustomerAppointment;

$appointment = hydrateCustomerAppointment( $row ) ;

$appointment->customer                     instanceof Customer         ; // true
$appointment->attendee[ 0 ]->workLocation  instanceof CustomerSite     ; // true
$appointment->report->followUp[ 0 ]        instanceof FollowUp         ; // true
$appointment->appointmentStatus            instanceof AppointmentDone  ; // true
```

Un appel suffit : la tête passe par `Reflection::hydrate()`, puis relit depuis la charge brute ce que la réflexion ne sait pas trancher ou ce qu'elle type moins profondément que l'aide. Ce qui n'est pas un tableau — une référence en chaîne, une instance déjà typée — ressort intact, et une liste qui ne résout rien rend `null` plutôt qu'un tableau brut résiduel.

🚨 **Le rendez-vous nommé en résultat d'une suite n'est pas déplié.** `FollowUp::$result` désigne une rencontre, dont le compte rendu porte des suites à donner, qui nomment des rencontres à leur tour : descendre par l'aide profonde ouvre un cycle que seule la donnée arrête. C'est une **référence**, typée sur un niveau et pas davantage — ce qu'elle porte elle-même reste brut, et qui en a besoin demande cette rencontre-là.

🔑 **Une liste de suites vide reste une liste vide**, là où le reste de la famille rend `null`. « Ce compte rendu n'a aucune suite » est une réponse, et ce n'est pas la réponse « rien n'était lisible ici » : un lecteur qui parcourt la valeur mérite une liste à parcourir.

🔑 **Trois propriétés sont laissées à leur attribut**, et c'est délibéré. `organizer`, `assignedCompany` et `location` sont déjà tranchées exactement par la réflexion, d'après le `@type` de la charge. Leur imposer une classe ferait pire que rien : une organisation simple relue en filiale, une salle virtuelle relue en site client. `assignedSeller` est la seule qui ait besoin de la main de l'aide — elle ne déclare aucun attribut et son union ne nomme qu'un `Person`, quand la propriété veut dire un `Seller`.

### Un statut se relit, et il se filtre

Les deux axes acceptent la constante nue ou la classe membre. Mesuré : la forme objet sérialise `{"@type":"AppointmentCancelled", …}` — le `@type` porte le **nom court**, jamais l'URI. Sans plus, les deux écritures ne se comparent à rien de commun, et un magasin ne peut plus filtrer « les rendez-vous annulés » sans abandonner l'une des deux.

Deux réponses, sans nouvelle propriété :

- **le membre pose son URI tout seul** — construit, il renseigne l'`additionalType` que `Thing` déclare déjà, avec l'URI que porte la constante nue. `??=` : un appelant reste libre d'imposer autre chose, et une valeur relue de la base n'est jamais écrasée ;
- **`hydrateAppointmentStatus()` et [`hydrateEventStatus()`](helpers.md#orgschemahelpershydrate--les-hydrateurs-purs)** relisent cet URI — puis, à défaut, le `@type` — et rendent l'instance de la bonne classe membre. La chaîne nue ressort intacte, un tableau redevient l'objet qui porte son motif.

```php
new AppointmentCancelled([ 'description' => 'Le client a annulé la veille.' ])->additionalType
    === AppointmentStatus::CANCELLED ; // true, avant comme après un aller-retour JSON
```

⚠️ **Les deux ne coïncident jamais côté maison** : le vocabulaire écrit `…/AppointmentStatus#NoShow` là où la classe s'appelle `AppointmentNoShow`. Aucune règle ne mène de l'un à l'autre — c'est précisément pourquoi l'URI est **déclaré** par le membre plutôt que dérivé de son nom.

---

## Constantes associées

Les clés de propriétés sont exposées par les traits [`CustomerAppointmentTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/CustomerAppointmentTrait.php), [`VisitReportTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/VisitReportTrait.php) et [`FollowUpTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/FollowUpTrait.php), composés dans l'agrégateur de domaine [`AppointmentsTrait`](../../../src/xyz/oihana/schema/constants/traits/AppointmentsTrait.php) et câblés dans la classe maîtresse [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php). Vous pouvez donc y accéder via `Oihana::APPOINTMENT_TYPE`, `Oihana::MOOD`, `Oihana::FOLLOW_UP`, etc. — et chaque classe expose les siennes (`CustomerAppointment::REPORT`).

Les propriétés reprises de Schema.org — `customer`, `attendee`, `location`, `organizer`, `makesOffer`, `assignedSeller`, `scheduledTime` — gardent leurs constantes existantes : elles ne sont pas redéclarées.

---

## Voir aussi

- [Vocabulaire Schema.org](../schema-org/README.md) — `Event`, `CreativeWork`, `Action` et `OpeningHoursSpecification`, le socle sur lequel tout repose.
- [Authentification](auth.md) — `User`, le compte dont l'agenda porte les rendez-vous, et ses disponibilités.
- [Personnes](people.md) — `Seller`, `CustomerEmployee` pour les contacts rencontrés.
- [Entités commerciales](organizations.md) — `Customer`, le sujet de toute rencontre.
- [Lieux](places.md) — `CustomerSite` et `JobSite`, où les rencontres ont lieu.
- [Documents commerciaux](business-documents.md) — les devis et commandes qui naissent d'une rencontre.
