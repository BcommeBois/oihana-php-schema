# Pourquoi une ontologie

Cette page explique le **parti pris** d'Oihana PHP Schema : modéliser le domaine sur une **ontologie** — un vocabulaire partagé et structuré — en s'appuyant sur **Schema.org** et en l'étendant, plutôt que d'inventer un modèle de données ad hoc. C'est ce choix qui rend la bibliothèque à la fois **standardisée** (elle parle une langue commune) et **évolutive** (elle se prolonge sans se renier).

C'est une page de **vision**, transverse à toute la bibliothèque. Pour la voir à l'œuvre sur un domaine concret, lisez [La gestion commerciale](oihana/gestion-commerciale.md), qui applique tout ce qui suit au cycle devis → facture.

> 🇬🇧 This page is also available in [English](../en/why-an-ontology.md).

---

## Le problème : chacun réinvente son modèle

Sans référentiel, chaque application redéfinit ses propres tables et ses propres noms : ici un client s'appelle `client`, là `customer`, ailleurs `acheteur` ; une facture porte un `montant_ht` dans un projet, un `total_excl_tax` dans un autre. Chacune de ces variantes est défendable — et c'est bien le problème : **rien ne se parle**. Faire dialoguer deux systèmes, exporter vers un format d'échange, indexer pour un moteur de recherche, tout cela demande alors un travail de traduction à refaire à chaque fois.

Une **ontologie** répond à cela : c'est un vocabulaire *partagé et explicite* — des types (`Person`, `Organization`, `Invoice`), leurs propriétés (`name`, `customer`, `totalPaymentDue`) et les relations entre eux. S'y conformer, c'est parler une langue que d'autres comprennent déjà.

---

## Pourquoi Schema.org comme socle

[Schema.org](https://schema.org) est l'ontologie la plus largement déployée au monde : co-portée par Google, Microsoft, Yahoo et Yandex, elle décrit ~800 types couvrant personnes, organisations, lieux, produits, offres, événements, actions… C'est le vocabulaire que lisent les moteurs de recherche, que consomment les assistants, qu'attendent d'innombrables outils.

S'appuyer dessus apporte, **gratuitement** :

- **une couverture immense déjà pensée** — inutile de redéfinir ce qu'est une `PostalAddress`, une `MonetaryAmount` ou une `Organization` : ces types existent, mûris par des années d'usage ;
- **l'interopérabilité** — un document produit par la bibliothèque est un document Schema.org, lisible par tout ce qui connaît ce vocabulaire ;
- **le JSON-LD** — chaque entité se sérialise en JSON-LD, le format du web sémantique, avec son `@type` et son `@context`, sans effort supplémentaire ;
- **une discipline de nommage** — quand un concept existe déjà dans Schema.org, on réutilise **son** nom, plutôt que d'en inventer un énième.

La bibliothèque fournit donc, dans le namespace `org\schema`, une **implémentation PHP typée** de ce vocabulaire : ~400 classes-valeurs, chacune miroir fidèle d'un type Schema.org.

---

## Le patron : un miroir intouché, une couche maison par-dessus

Schema.org est vaste mais **volontairement généraliste** : il ne connaît ni le SIRET français, ni l'éco-participation, ni la note de débit, ni la balance âgée. Une vraie solution métier a besoin de ces concepts. La bibliothèque les ajoute — mais sans jamais dénaturer le socle. D'où deux couches nettement séparées :

| Couche | Namespace | Rôle | Règle d'or |
|--------|-----------|------|------------|
| **Le miroir** | `org\schema` | Copie fidèle et typée de Schema.org. | On n'y touche pas — il reste une reproduction exacte, prévisible pour quiconque connaît Schema.org. |
| **La couche maison** | `xyz\oihana\schema` | Les types métier absents de Schema.org, ou qui le spécialisent. | Elle **dépend** du miroir, jamais l'inverse. |

Le sens de la dépendance est la clé : la couche maison s'appuie sur le miroir (une `Invoice` maison étend `Intangible`, réutilise `MonetaryAmount`…), mais le miroir **ne dépend jamais** de la couche maison. On peut ainsi faire évoluer le métier sans risque de casser le socle, et un lecteur qui connaît Schema.org retrouve `org\schema` exactement tel qu'il l'attend.

Les documents maison se signent d'ailleurs d'un contexte distinct — `@context = 'https://schema.oihana.xyz'` — pour qu'on sache, à la lecture d'un JSON-LD, ce qui relève du standard et ce qui relève de l'extension.

---

## Réutiliser les noms d'abord, n'inventer qu'en dernier recours

La règle de nommage de la couche maison est simple : **si Schema.org a déjà un nom pour le concept, on le reprend**. On n'invente un nom nouveau que pour un concept réellement absent du standard.

- Un devis a une date de fin de validité ? Schema.org porte déjà `validThrough` sur `Offer`/`PriceSpecification` — on le réutilise, plutôt que d'inventer `validUntil`.
- Un document a un client et un vendeur ? `customer` et `seller` existent — on les garde.
- En revanche, une ventilation de TVA, un échéancier, une balance âgée n'existent pas dans Schema.org — là, on crée, en s'inspirant des standards du domaine (UBL, Peppol) sans en recopier la lourdeur.

Cette discipline maximise la compatibilité : plus on parle le vocabulaire du standard, plus on est compris — et moins on accumule de noms maison à maintenir et à documenter.

---

## Ce qui rend la bibliothèque évolutive

Standardiser ne doit pas figer. Plusieurs choix de conception font que la couche maison **se prolonge** au lieu de se réécrire :

- **Des énumérations à valeurs libres.** Une énumération comme `PaymentReminderLevel` ou `StatementEntryType` propose des membres, mais accepte aussi un libellé libre, ou une **sous-classe** ajoutant vos propres valeurs. Votre projet enrichit sans forker.
- **Un typage tolérant à l'hydratation.** Une propriété structurée est typée `null|array|X` (jamais un strict `?X`), parce qu'une donnée arrive presque toujours d'abord sous forme de tableau (JSON décodé, ligne de base). La bibliothèque construit d'abord, hydrate profondément ensuite — les deux chemins coexistent sans friction.
- **Des constantes de propriétés.** Chaque propriété a sa constante (`Invoice::PAYMENT_STATUS`, `Oihana::TOTALS`), agrégées dans un point d'accès unique. Écrire du code contre ces constantes, c'est se protéger des fautes de frappe et rendre les renommages sûrs.
- **La sous-classe comme extension naturelle.** Un besoin propre à votre métier ? Étendez la classe maison, ajoutez vos propriétés, gardez tout l'acquis (sérialisation, hydratation, constantes). Le cycle de documents commerciaux est né exactement ainsi : parti des `Invoice`/`Order` de Schema.org, il s'est étendu en une hiérarchie complète — sans jamais modifier le miroir.

---

## Étude de cas : le cycle des documents commerciaux

La [couche des documents commerciaux](oihana/business-documents.md) illustre toute cette démarche à l'échelle réelle :

1. **On part du standard.** Schema.org a déjà `Invoice` et `Order`. On les prend comme point de départ conceptuel.
2. **On ancre sans dénaturer.** Le `BusinessDocument` maison étend `Intangible` (comme le font `Invoice`/`Order` dans le miroir), plutôt que de sous-classer directement le miroir — celui-ci reste intact.
3. **On réutilise les noms.** `customer`, `seller`, `validThrough`, `paymentStatus`, `billingPeriod`… autant de noms empruntés à Schema.org.
4. **On complète les manques.** Ventilation de taxes, ajustements, échéanciers, relances, balance âgée, avoir, note de débit, confirmation de réception… — les concepts absents du standard, inspirés d'UBL/Peppol.
5. **Ça a grandi sans se réécrire.** Le cycle est passé, lot après lot, d'une poignée de classes à près de trente — chaque ajout se posant sur l'acquis, aucun ne remettant en cause le socle ni le miroir.

C'est la démonstration concrète de la promesse : **standardisé** parce qu'ancré sur Schema.org, **évolutif** parce que la couche maison est faite pour s'étendre.

---

## Pour aller plus loin

- [La gestion commerciale](oihana/gestion-commerciale.md) — la démarche appliquée au cycle devis → facture.
- [Documents commerciaux](oihana/business-documents.md) — la référence technique de la couche métier la plus aboutie.
- [Vocabulaire Schema.org](schema-org/README.md) — le socle `org\schema` : ce que le miroir couvre.
- [Les extensions Oihana](oihana/README.md) — le tour des namespaces de la couche maison.
- [Démarrage rapide](demarrage.md) — installation, hydratation, sérialisation JSON-LD.
