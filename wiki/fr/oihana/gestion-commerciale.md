# La gestion commerciale avec Oihana

Ce guide explique **le cycle de gestion commerciale** modélisé par la bibliothèque — de la première proposition faite à un client jusqu'au relevé de compte de fin de période — et clarifie **ce qui distingue chaque document** : à quoi sert un devis, en quoi un bon de commande diffère d'une facture, pourquoi un avoir n'est pas une note de débit, etc.

Il s'agit d'une lecture **métier**, complémentaire de la [référence technique `business-documents`](business-documents.md), qui détaille classe par classe les propriétés et l'hydratation. Ici, on raconte le *pourquoi* et l'*enchaînement* ; là-bas, on trouve le *comment*.

> 🇬🇧 This page is also available in [English](../../en/oihana/commercial-management.md).

---

## Le cycle en un coup d'œil

Une transaction commerciale suit presque toujours la même trame : on propose, on commande, on livre, on facture, on paie — et l'on corrige ou récapitule si nécessaire. La bibliothèque modélise chaque étape par un document dédié, tous descendant d'un parent commun, [`BusinessDocument`](business-documents.md#businessdocument).

```
        VENDEUR ─────────────────────────────────────────────► ACHETEUR
           │                                                       │
   1.  Devis (Quote)  ─────────────── proposition ───────────────► │
           │                                                       │
           │ ◄──────────── 2. Bon de commande (PurchaseOrder) ─────┤ engagement
           │                                                       │
   3.  Bon de livraison (DeliveryNote) ──── biens expédiés ──────► │
           │                                                       │
           │ ◄──── 4. Confirmation de réception ───────────────────┤ (GoodsReceiptConfirmation)
           │                                                       │
   5.  Facture (Invoice) ──────────────── créance ───────────────► │
           │                                                       │
           │      ± Avoir (CreditNote) / Note de débit (DebitNote) │ correction
           │                                                       │
           │ ◄──────────── 6. Paiement ────────────────────────────┤
   7.  Reçu (Receipt) ◄──────────────► Avis de paiement (RemittanceAdvice)
           │                                                       │
   8.  Relevé de compte (Statement) ──── récapitulatif période ──► │
```

Toutes ces étapes ne sont pas obligatoires : une vente au comptant peut sauter directement au reçu, un service peut être facturé sans bon de livraison. Le cycle est une **trame**, pas une contrainte — chaque document existe indépendamment et se relie aux autres quand c'est pertinent.

---

## Qui émet quoi, quand, et ce que ça engage

C'est le tableau de référence pour distinguer les documents. La colonne **engage** indique la portée de chaque pièce : une simple information, une proposition révocable, ou un engagement ferme.

| # | Document | Classe | Émis par | À quel moment | Ce que ça engage |
|---|----------|--------|----------|---------------|------------------|
| 1 | **Devis** | [`Quote`](business-documents.md#quote) | Vendeur | Avant toute commande | Une **proposition** de prix, valable jusqu'à une date (`validThrough`). N'engage pas l'acheteur ; engage le vendeur sur son prix tant que le devis est valide. |
| 2 | **Bon de commande** | [`PurchaseOrder`](business-documents.md#purchaseorder) | Acheteur | Après acceptation du devis | L'**engagement ferme** de l'acheteur à acheter. Référence le(s) devis d'origine (`referencesQuote`). |
| 3 | **Bon de livraison** | [`DeliveryNote`](business-documents.md#deliverynote) | Vendeur | À l'expédition des biens | Atteste **ce qui a été expédié** (quantités livrées vs commandées, par ligne). Ne réclame pas de paiement. |
| 4 | **Confirmation de réception** | [`GoodsReceiptConfirmation`](business-documents.md#goodsreceiptconfirmation) | Acheteur | À la réception des biens | Atteste **ce qui a été reçu** (quantités, état, écarts). Base d'un éventuel avoir ou litige. |
| 5 | **Facture** | [`Invoice`](business-documents.md#invoice) | Vendeur | Après livraison / prestation | Une **créance** exigible : la somme due, sa date d'échéance, son statut de paiement. Référence le(s) bon(s) de commande (`referencesOrder`). |
| 6a | **Avoir** | [`CreditNote`](business-documents.md#creditnote) | Vendeur | Après facturation, si correction à la baisse | **Réduit** ce qui est dû (retour, erreur de prix, geste commercial). Référence la(les) facture(s) corrigée(s). |
| 6b | **Note de débit** | [`DebitNote`](business-documents.md#debitnote) | Vendeur | Après facturation, si correction à la hausse | **Augmente** ce qui est dû (sous-facturation, ligne oubliée). L'inverse symétrique de l'avoir. |
| 7a | **Reçu** | [`Receipt`](business-documents.md#receipt) | Vendeur | À la réception du paiement | La **preuve, côté vendeur**, qu'un paiement a été reçu. |
| 7b | **Avis de paiement** | [`RemittanceAdvice`](business-documents.md#remittanceadvice) | Acheteur (payeur) | À l'émission du paiement | Le **détail, côté payeur**, d'un règlement : quelles factures il solde, pour quel montant. |
| 8 | **Relevé de compte** | [`Statement`](business-documents.md#statement) | Vendeur | Périodiquement | Le **récapitulatif** des mouvements d'un compte sur une période : solde d'ouverture et de clôture, balance âgée. |

---

## Vendeur ou acheteur : deux points de vue en miroir

La hiérarchie a longtemps été pensée du seul point de vue du **vendeur** (celui qui propose, livre, facture, encaisse). Mais plusieurs documents ont un **pendant côté acheteur**, qui décrit le même événement vu de l'autre bout de la transaction :

| Événement | Vu par le vendeur | Vu par l'acheteur |
|-----------|-------------------|-------------------|
| Les biens changent de mains | [`DeliveryNote`](business-documents.md#deliverynote) — « voici ce que j'expédie » | [`GoodsReceiptConfirmation`](business-documents.md#goodsreceiptconfirmation) — « voici ce que j'ai reçu » |
| L'argent change de mains | [`Receipt`](business-documents.md#receipt) — « j'ai bien reçu votre paiement » | [`RemittanceAdvice`](business-documents.md#remittanceadvice) — « voici le paiement que je vous adresse » |

Les deux versions **coexistent volontairement** : un logiciel agissant pour le vendeur modélise sa vue, un logiciel côté acheteur modélise la sienne, sans surcharger le type de l'autre. Quiconque n'a besoin que d'un seul côté ignore simplement l'autre.

Sur chaque document, les deux parties sont toujours nommées de la même façon — `customer` (le client) et `seller` (le vendeur), les noms hérités de Schema.org — quel que soit le sens de lecture.

Pour dire explicitement **de quel côté on se place**, tout `BusinessDocument` peut porter une propriété `direction` (→ [`BusinessDocumentDirection`](core.md#businessdocumentdirection)) : `SALE` quand l'opérateur est le vendeur (un document sortant, de vente), `PURCHASE` quand il est le client (un document entrant, d'achat). Elle est **orthogonale** au type du document et à son statut de cycle de vie : elle ne fait qu'indiquer laquelle des parties `seller`/`customer` est l'organisation de l'opérateur — de quoi faire servir un même modèle indifféremment à la vue vendeur ou acheteur.

---

## Le fil qui relie les documents

Chaque maillon référence son amont via une propriété `references*`, ce qui permet de remonter toute la chaîne d'une transaction :

```
Quote ◄── referencesQuote ── PurchaseOrder ◄── referencesOrder ── Invoice ◄── referencesInvoice ── CreditNote / DebitNote / Receipt / RemittanceAdvice
                                                                    │
DeliveryNote ◄── referencesDeliveryNote ── GoodsReceiptConfirmation │
```

Ces liens sont **des collections** : un document peut en référencer plusieurs. C'est la réalité du terrain — une facture de regroupement porte plusieurs bons de commande, un seul paiement solde plusieurs factures, une commande peut agréger plusieurs devis acceptés. Voir la [référence technique](business-documents.md#exemple-express) pour l'hydratation de ces liens.

---

## Le statut : où en est un document dans son cycle de vie

Indépendamment de son type, tout document porte un **statut de cycle de vie** ([`BusinessDocumentStatus`](core.md#businessdocumentstatus)), distinct du statut de *paiement* d'une facture. Il décrit où en est la pièce elle-même :

| Statut | Signification |
|--------|---------------|
| `DRAFT` | En préparation, pas encore envoyé. |
| `SENT` | Transmis au destinataire. |
| `ACCEPTED` | Accepté par le destinataire (un devis devenu commande, par ex.). |
| `REJECTED` | Refusé par le destinataire. |
| `EXPIRED` | Période de validité écoulée (un devis passé sa date). |
| `CONVERTED` | Transformé en un autre document (un devis converti en commande). |
| `CANCELLED` | Annulé après émission. |

Ce cycle de vie est **générique** : il vaut pour un devis comme pour une facture. Le suivi financier fin (une échéance est-elle payée, en retard ?) relève d'un mécanisme distinct — voir ci-dessous.

---

## Le suivi financier : échéances, relances, recouvrement

Au-delà des documents eux-mêmes, la bibliothèque outille le **suivi du paiement** :

- Un **échéancier** ([`PaymentSchedule`](business-documents.md#paymentschedule)) étale un règlement sur plusieurs **échéances** ([`PaymentInstallment`](business-documents.md#paymentinstallment)), chacune avec sa date, son montant et son propre statut de paiement (payée, due, en retard).
- Une échéance ou un échéancier peut porter ses **relances** ([`PaymentReminder`](business-documents.md#paymentreminder)) — la trace des rappels envoyés au client, avec leur niveau d'escalade (du simple rappel à la mise en demeure) et leurs éventuels frais de retard.
- Le **relevé de compte** ([`Statement`](business-documents.md#statement)) récapitule les mouvements d'un compte et expose une **balance âgée** ([`AgingSummary`](business-documents.md#agingsummary)) — la répartition du solde par ancienneté de retard (à échoir, 1-30 jours, 31-60, etc.), l'outil de pilotage du recouvrement.

Un principe traverse toute cette couche : la bibliothèque **consigne**, elle ne **décide** pas. Elle modélise ce qu'est une relance ou une balance âgée ; elle ne contient ni la logique qui décide *quand* relancer, ni le calcul qui remplit les tranches d'ancienneté. Ces règles restent la responsabilité de l'application qui consomme la bibliothèque.

---

## Corriger une facture : avoir ou note de débit

Une facture émise ne se modifie pas — on la **corrige par un nouveau document**, ce qui préserve la piste d'audit :

- pour **réduire** ce qui est dû (marchandise retournée, erreur de prix, geste commercial) : un **avoir** ([`CreditNote`](business-documents.md#creditnote)) ;
- pour **augmenter** ce qui est dû (sous-facturation, prestation supplémentaire) : une **note de débit** ([`DebitNote`](business-documents.md#debitnote)).

Dans les deux cas, le montant correctif circule par le récapitulatif monétaire hérité (`totals`), toujours en positif : c'est le **type du document** qui porte le sens (« ceci réduit » ou « ceci augmente »), jamais une convention de signe cachée dans les montants. Un avoir peut n'être que partiellement consommé — sa part non encore imputée est portée par `remainingBalance`, et son sort (remboursé, ré-appliqué, en attente) par `disposition`.

---

## Pour aller plus loin

- [Documents commerciaux](business-documents.md) — la référence technique : catalogue des classes, propriétés, exemples d'hydratation.
- [Pourquoi une ontologie](../pourquoi-une-ontologie.md) — la vision : pourquoi s'appuyer sur Schema.org et l'étendre plutôt que repartir de zéro.
- [Couche commerce](products.md) — `Product`, `PriceComponentType` (réutilisée par les ajustements), les unités de vente.
- [Entités commerciales](organizations.md) / [Personnes](people.md) — les parties d'une transaction : `Customer`, `Provider`, `Seller`, `Company`.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
