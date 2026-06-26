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
| [`xyz\oihana\schema\auth`](oihana-auth.md) | [Authentification & RBAC](oihana-auth.md)    | OAuth2/OIDC, sessions, keyfiles, utilisateurs, rôles, permissions, politiques compatibles Casbin. |
| [`xyz\oihana\schema\business`](oihana-business.md) | [Métier Oihana](oihana-business.md)    | `BusinessIdentity` (lien typé compte ↔ entité) et `UserProfile` (gabarit de provisionnement à la création). |
| [`xyz\oihana\schema\http`](oihana-http.md) | [HTTP Oihana](oihana-http.md)            | Métadonnées structurées de requête HTTP : `UserAgentInfo` (navigateur, OS, classe d'appareil, indicateur bot). |
| [`xyz\oihana\schema\places`](oihana-places.md) | [Lieux Oihana](oihana-places.md)         | Emplacements opérationnels : `Site`, `Office`, `Warehouse`, `JobSite`.                      |
| [`xyz\oihana\schema\thesaurus`](oihana-thesaurus.md) | [Thésaurus Oihana (SKOS)](oihana-thesaurus.md) | Arbres de concepts SKOS : `ThesaurusTerm`, `Concept`, `ProductCategoryTerm`, `ConceptScheme`, `Collection`, `OrderedCollection` — hiérarchie, notes, alignements, collections. |
| [`xyz\oihana\schema`](oihana-core.md)   | [Types transverses Oihana](oihana-core.md)     | `Pagination`, `Log`, `AuditAction`, énumérations d'audit.                                   |
| [`com\progress\schema`](openedge-progress.md) | [Catalogue système OpenEdge Progress](openedge-progress.md) | Tables `SYS%` du catalogue système : tables, colonnes, index, vues, utilisateurs, privilèges, contraintes, séquences, triggers, procédures, types de données. |

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
