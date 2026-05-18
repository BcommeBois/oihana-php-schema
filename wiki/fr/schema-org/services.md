# Services — `org\schema\services`

Le namespace `org\schema\services` contient les **types de services spécialisés** de Schema.org — actuellement centrés sur les produits financiers et services de paiement qui étendent `org\schema\Service`.

> 🇬🇧 This page is also available in [English](../../en/schema-org/services.md).

---

## Quand l'utiliser

Utilisez ces classes chaque fois que vous décrivez une offre financière ou un service lié au paiement :

- un produit bancaire (`BankAccount`, `LoanOrCredit`, `InvestmentOrDeposit`),
- un instrument de paiement (`PaymentCard`),
- un service de paiement ou de conversion de devises (`PaymentService`, `CurrencyConversionService`),
- un produit financier générique (`FinancialProduct`).

Pour les services non financiers, utilisez la classe `Service` de premier niveau — voir [types de base](core.md).

---

## Exemple express

```php
use org\schema\services\BankAccount;
use org\schema\Organization;
use org\schema\constants\Schema;

$account = new BankAccount
([
    Schema::NAME        => 'Compte Courant Pro' ,
    Schema::PROVIDER    => new Organization([ Schema::NAME => 'Banque Fictive' ]) ,
    Schema::URL         => 'https://example.bank/products/compte-courant-pro' ,
    Schema::DESCRIPTION => 'Compte courant dédié aux professionnels indépendants.' ,
]);
```

---

## Catalogue des classes

| Classe                      | Rôle                                                                |
|-----------------------------|---------------------------------------------------------------------|
| `FinancialProduct`          | Produit financier générique (classe de base).                        |
| `BankAccount`               | Produit de compte bancaire.                                          |
| `LoanOrCredit`              | Produit de prêt ou de crédit.                                        |
| `InvestmentOrDeposit`       | Produit d'investissement ou de dépôt.                                |
| `PaymentCard`               | Carte de paiement (crédit, débit, …).                                |
| `PaymentService`            | Service de traitement de paiement.                                   |
| `CurrencyConversionService` | Service de conversion de devises.                                    |

Pour la liste exhaustive et le jeu complet des propriétés de chaque classe, parcourez [`src/org/schema/services/`](../../../src/org/schema/services).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
