# `xyz\oihana\schema` — Les extensions Oihana

Le namespace `xyz\oihana\schema` est la **couche maison** de la bibliothèque : les types métier qui n'existent pas dans Schema.org ou qui le spécialisent. Il s'appuie sur le [vocabulaire `org\schema`](../schema-org/README.md) — jamais l'inverse — et signe ses documents du contexte `https://schema.oihana.xyz`.

> 🇬🇧 This page is also available in [English](../../en/oihana/README.md).

---

## Les guides

| Guide | Namespace | Couverture |
|---|---|---|
| [Authentification & RBAC](auth.md) | `…\auth` | OAuth2/OIDC, sessions, keyfiles, utilisateurs, rôles, permissions, politiques compatibles Casbin. |
| [Métier](business.md) | `…\business` | `BusinessIdentity` (lien typé compte ↔ entité) et `UserProfile` (gabarit de provisionnement). |
| [Documents commerciaux](business-documents.md) | `…\business\documents` | Le cycle devis → bon de commande → facture (et ses à-côtés) : `BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement`/`StatementEntry`, l'export `BusinessDocumentExporter`/`JsonLdExporter`, plus les objets de valeur transverses `TaxDetail`, `Adjustment`, `EcoFeeRule`/`AppliedEcoFee`, `DocumentTotals`, `BusinessDocumentLine`, `PaymentSchedule`/`PaymentInstallment`. |
| [Entités commerciales](organizations.md) | `…\organizations` | `Company` (SIRET/APE, ingestion adresse + contacts) et ses déclinaisons `Customer`, `Provider`, `Subsidiary`, `Affiliate`. |
| [Personnes](people.md) | `…\people` | `Person` et ses déclinaisons typées `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee`, `SubsidiaryEmployee`. |
| [Couche commerce](products.md) | `…\products` | `Product` (arbre des quantités éligibles, conversions, hook `resolveUnitCode()`) et ses satellites. |
| [Lieux](places.md) | `…\places` | Emplacements opérationnels : `Site`, `Office`, `Warehouse`, `JobSite`. |
| [Thésaurus (SKOS)](thesaurus.md) | `…\thesaurus` | Arbres de concepts SKOS et couche registre (`ThesaurusScheme`, `ThesaurusDomain`). |
| [HTTP](http.md) | `…\http` | Métadonnées structurées de requête : `UserAgentInfo`. |
| [Types transverses](core.md) | racine | `Pagination`, `Log`, `AuditAction`, énumérations d'audit. |

## Les mécanismes

| Guide | Couverture |
|---|---|
| [Ingestion](ingestion.md) | Les ponts `__set` qui hydratent une ligne plate en objet imbriqué (adresse, contacts, géolocalisation, propriétés additionnelles). |
| [Fonctions d'assistance](helpers.md) | Les fonctions autoloadées : hydrateurs (relecture typée des documents) et pivots de compte. |

---

## Voir aussi

- [Vocabulaire Schema.org](../schema-org/README.md) — le socle `org\schema` sur lequel tout repose.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, sérialisation JSON-LD.
