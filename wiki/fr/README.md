# Oihana PHP Schema — Documentation française

Bienvenue dans les guides francophones d'**Oihana PHP Schema**.

Ce wiki est un compagnon rédigé à la main de la [référence d'API](../../docs) générée automatiquement. Il explique le *pourquoi* derrière l'architecture et vous accompagne dans les workflows les plus courants.

> 🇬🇧 This page is also available in [English](../en/README.md).

---

## 📚 Sommaire

### Fondamentaux

- [Démarrage rapide](demarrage.md) — installation, hydratation d'un premier `Thing`, sérialisation JSON-LD, utilisation sûre des constantes.

### Schémas par namespace

| Namespace                              | Guide                                          | Couverture                                                                                  |
|----------------------------------------|------------------------------------------------|---------------------------------------------------------------------------------------------|
| [`org\schema`](schema-org/README.md)           | [Vocabulaire Schema.org](schema-org/README.md)        | ~400 objets-valeur typés pour Schema.org — `Thing`, `Person`, `Place`, `Event`, `Product`, `Offer`, hiérarchie complète des `Action`, … |
| [`xyz\oihana\schema\auth`](oihana/auth.md) | [Authentification & RBAC](oihana/auth.md)    | OAuth2/OIDC, sessions, keyfiles, utilisateurs, rôles, permissions, politiques compatibles Casbin. |
| [`xyz\oihana\schema\business`](oihana/business.md) | [Métier Oihana](oihana/business.md)    | `BusinessIdentity` (lien typé compte ↔ entité) et `UserProfile` (gabarit de provisionnement à la création). |
| [`xyz\oihana\schema\organizations`](oihana/organizations.md) | [Entités commerciales](oihana/organizations.md) | `Company` (SIRET/APE, ingestion adresse + contacts) et ses déclinaisons `Customer`, `Provider`, `Subsidiary`, `Affiliate`. |
| [`xyz\oihana\schema\people`](oihana/people.md) | [Personnes](oihana/people.md)                  | `Person` et ses déclinaisons typées `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee`, `SubsidiaryEmployee`. |
| [`xyz\oihana\schema\products`](oihana/products.md) | [Couche commerce](oihana/products.md)      | `Product` (arbre des quantités éligibles, conversions d'unité de vente, hook `resolveUnitCode()`) et ses satellites : `StockLevel`, `TaxRate`, `PriceSegmentation`, conditions de paiement, informations fournisseur/dépôt. |
| [`xyz\oihana\schema\http`](oihana/http.md) | [HTTP Oihana](oihana/http.md)            | Métadonnées structurées de requête HTTP : `UserAgentInfo` (navigateur, OS, classe d'appareil, indicateur bot). |
| [`xyz\oihana\schema\places`](oihana/places.md) | [Lieux Oihana](oihana/places.md)         | Emplacements opérationnels : `Site`, `Office`, `Warehouse`, `JobSite`.                      |
| [`xyz\oihana\schema\thesaurus`](oihana/thesaurus.md) | [Thésaurus Oihana (SKOS)](oihana/thesaurus.md) | Arbres de concepts SKOS : `ThesaurusTerm`, `Concept`, `ProductCategoryTerm`, `ConceptScheme`, `Collection`, `OrderedCollection` — hiérarchie, notes, alignements, collections — plus la couche registre (`ThesaurusScheme`, `ThesaurusDomain`). |
| [`xyz\oihana\schema`](oihana/core.md)   | [Types transverses Oihana](oihana/core.md)     | `Pagination`, `Log`, `AuditAction`, énumérations d'audit.                                   |
| [`com\progress\schema`](openedge-progress.md) | [Catalogue système OpenEdge Progress](openedge-progress.md) | Tables `SYS%` du catalogue système : tables, colonnes, index, vues, utilisateurs, privilèges, contraintes, séquences, triggers, procédures, types de données. |

### Mécanismes transverses

- [Ingestion](oihana/ingestion.md) — les ponts `__set` qui hydratent une ligne plate de jeu de données en objet imbriqué (adresse, contacts, géolocalisation, propriétés additionnelles).

### Fonctions d'assistance

- [Hydratation et pivots](oihana/helpers.md) — les fonctions libres autoloadées : hydrater une donnée brute en objet de schéma typé (références imbriquées comprises) et résoudre les identités métier d'un compte en clés de périmètre.

### À venir

D'autres pages seront ajoutées progressivement :

- Génération de JSON Schema à partir de propriétés typées (`composer schemas:all`).
- Étendre la bibliothèque avec vos propres types métier.
- Hydratation approfondie : types union, objets imbriqués, métadonnées ArangoDB.

---

## 🔗 Liens utiles

- 📖 [Référence d'API générée](../../docs) — toutes les classes, propriétés et méthodes.
- 🧱 [`README.md`](../../README.md) — vue d'ensemble du dépôt et exemple express.
- 📝 [`CHANGELOG.md`](../../CHANGELOG.md) — changements notables entre versions.
- 🐙 [Dépôt GitHub](https://github.com/BcommeBois/oihana-php-schema)
