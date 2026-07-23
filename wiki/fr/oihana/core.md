# `xyz\oihana\schema` — Types transverses Oihana

Le sommet du namespace `xyz\oihana\schema` regroupe les **extensions Oihana transverses** qui n'appartiennent à aucun sous-namespace spécialisé : métadonnées de pagination pour les collections, entrées de log et enregistrements d'actions auditables.

> 🇬🇧 This page is also available in [English](../../en/oihana/core.md).

---

## Quand l'utiliser

| Besoin                                                                      | Classe                                   |
|-----------------------------------------------------------------------------|------------------------------------------|
| Décrire la pagination d'un endpoint ou d'une requête.                       | [`Pagination`](#pagination)              |
| Persister ou transporter une entrée de log structurée.                      | [`Log`](#log)                            |
| Enregistrer une action auditable (create / update / delete / login / logout). | [`AuditAction`](#auditaction)         |
| Énumérer les types d'actions d'audit.                                       | [`AuditActionType`](../../src/xyz/oihana/schema/enumerations/AuditActionType.php) |
| Énumérer les types de canaux de contact.                                    | [`ContactType`](../../src/xyz/oihana/schema/enumerations/ContactType.php) |
| Énumérer le statut de cycle de vie d'un document métier.                    | [`BusinessDocumentStatus`](#businessdocumentstatus) |
| Énumérer la direction commerciale d'un document métier (vente / achat).     | [`BusinessDocumentDirection`](#businessdocumentdirection) |

Toutes les entités partagent le distinguisheur `@context = 'https://schema.oihana.xyz'`.

---

## <a id="pagination"></a> `Pagination`

`Pagination` étend `org\schema\Intangible` et modélise tout ce qui est typiquement nécessaire pour décrire une collection paginée : `page`, `limit`, `offset`, `numberOfPages`, ainsi que les bornes optionnelles `minLimit` et `maxLimit`.

```php
use xyz\oihana\schema\Pagination;
use xyz\oihana\schema\constants\Oihana;

$pagination = new Pagination
([
    Oihana::PAGE            => 2  ,
    Oihana::LIMIT           => 50 ,
    Oihana::NUMBER_OF_PAGES => 10 ,
    Oihana::OFFSET          => 50 ,
]);

echo json_encode( $pagination , JSON_UNESCAPED_SLASHES );
// {"@type":"Pagination","@context":"https://schema.oihana.xyz","page":2,"limit":50,"numberOfPages":10,"offset":50}
```

Les constantes de propriétés sont exposées via `Oihana::PAGE`, `Oihana::LIMIT`, `Oihana::OFFSET`, `Oihana::NUMBER_OF_PAGES`, `Oihana::MIN_LIMIT`, `Oihana::MAX_LIMIT`.

---

## <a id="log"></a> `Log`

`Log` étend `org\schema\Thing` et représente une entrée de log unique avec `date`, `time`, `level` et `message`. La classe est volontairement légère — l'objectif est de *transporter* un enregistrement de log à travers des frontières systèmes (file de messages, ligne en base, ligne JSON structurée), pas de remplacer un logger complet.

```php
use xyz\oihana\schema\Log;

$entry = new Log
([
    'date'    => '2026-05-18' ,
    'time'    => '14:32:10' ,
    'level'   => 'INFO' ,
    'message' => 'Application started successfully.' ,
]);

echo (string) $entry;
// 2026-05-18 14:32:10 INFO Application started successfully.
```

---

## <a id="auditaction"></a> `AuditAction`

`AuditAction` est l'**action auditable** version Oihana — un enregistrement structuré de *qui a fait quoi, quand, sur quelle cible, avec quel résultat*. Elle est conçue pour être persistée (par exemple dans une collection ou table `audit`) et consommée par les tableaux de bord d'administration, les investigations sécurité et les pistes d'audit conformes RGPD.

Elle porte :

- l'acteur et la requête qui a déclenché l'action,
- un tag `event` métier (créé par l'appelant),
- un `outcome` lisible machine (success / denied / error / …),
- le `type` d'action (`CREATE`, `UPDATE`, `DELETE`, `ADD`, `LOGIN`, `LOGOUT`, `REJECT`) — voir l'énumération [`AuditActionType`](../../src/xyz/oihana/schema/enumerations/AuditActionType.php).

Les constantes des clés de propriétés `AuditAction` sont exposées par le trait [`AuditTrait`](../../src/xyz/oihana/schema/constants/traits/AuditTrait.php) et accessibles via l'agrégateur global `Oihana`.

---

## <a id="businessdocumentstatus"></a> `BusinessDocumentStatus`

`BusinessDocumentStatus` énumère le **statut de cycle de vie** d'un document métier (devis, bon de commande, facture…) : `DRAFT`, `SENT`, `ACCEPTED`, `REJECTED`, `EXPIRED`, `CONVERTED`, `CANCELLED`. Elle étend `org\schema\enumerations\StatusEnumeration` et se distingue de l'`OrderStatus` de Schema.org, qui suit le statut de *livraison* d'une commande (expédiée, en transit…), pas celui du document.

Elle est consommée par [`BusinessDocument::$status`](business-documents.md#businessdocument) et l'ensemble de la hiérarchie de documents commerciaux (`xyz\oihana\schema\business\documents`).

---

## <a id="businessdocumentdirection"></a> `BusinessDocumentDirection`

`BusinessDocumentDirection` énumère la **direction commerciale** d'un document métier du point de vue de l'organisation qui l'exploite : `SALE` (l'opérateur est le vendeur — un document sortant, de vente) et `PURCHASE` (l'opérateur est le client — un document entrant, d'achat). Elle étend `org\schema\Enumeration`.

Elle est **orthogonale** au type du document (devis, commande, facture…) et à son cycle de vie [`BusinessDocumentStatus`](#businessdocumentstatus) : elle indique seulement laquelle des parties — le `seller` ou le `customer` — est l'organisation de l'opérateur. Elle est consommée par [`BusinessDocument::$direction`](business-documents.md#businessdocument).

---

## Pour aller plus loin

- [`org\schema`](../schema-org/README.md) — classes de base (`Intangible`, `Thing`, `Action`).
- [`xyz\oihana\schema\auth`](auth.md) — entités d'authentification souvent référencées par les enregistrements d'audit.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
