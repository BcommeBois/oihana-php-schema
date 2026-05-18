# Services — `org\schema\services`

The `org\schema\services` namespace contains the **specialised service types** of Schema.org — currently focused on financial products and payment services that extend `org\schema\Service`.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/services.md).

---

## When to use it

Reach for these classes whenever you describe a financial offering or a payment-related service:

- a bank product (`BankAccount`, `LoanOrCredit`, `InvestmentOrDeposit`),
- a payment instrument (`PaymentCard`),
- a payment service or currency-conversion service (`PaymentService`, `CurrencyConversionService`),
- a generic financial product (`FinancialProduct`).

For non-financial services, use the top-level `Service` class — see [core types](core.md).

---

## Quick example

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

## Class catalog

| Class                       | Role                                                                |
|-----------------------------|---------------------------------------------------------------------|
| `FinancialProduct`          | Generic financial product (base class).                              |
| `BankAccount`               | Bank account product.                                                |
| `LoanOrCredit`              | Loan or credit product.                                              |
| `InvestmentOrDeposit`       | Investment or deposit product.                                       |
| `PaymentCard`               | Payment card product (credit, debit, …).                              |
| `PaymentService`            | Payment-processing service.                                          |
| `CurrencyConversionService` | Currency-conversion service.                                         |

For the exhaustive list and the full property set of each class, browse [`src/org/schema/services/`](../../../src/org/schema/services).

---

## Up to

[← `org\schema` overview](README.md)
