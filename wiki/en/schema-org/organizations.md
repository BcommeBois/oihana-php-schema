# Organizations — `org\schema\organizations`

The `org\schema\organizations` namespace contains the **~18 specialised organization types** that extend `org\schema\Organization` — corporations, NGOs, governmental bodies, educational institutions, libraries, sports and medical organizations, and more.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/organizations.md).

---

## When to use it

Use these classes whenever an `Organization` needs a more precise identity:

- a `Corporation`, `Cooperative` or `Consortium` for incorporated entities,
- an `NGO`, `PoliticalParty` or `WorkersUnion` for civil-society organizations,
- a `GovernmentOrganization` or `SearchRescueOrganization` for public bodies,
- an `EducationalOrganization`, `LibrarySystem` or `MedicalOrganization` for institutions,
- a `LocalBusiness`, `OnlineBusiness` for commerce,
- a `FundingScheme`, `Project` or `ResearchOrganisation` for research and grants.

---

## Quick example

```php
use org\schema\organizations\EducationalOrganization;
use org\schema\PostalAddress;
use org\schema\constants\Schema;

$university = new EducationalOrganization
([
    Schema::NAME    => 'Université de Nantes' ,
    Schema::URL     => 'https://www.univ-nantes.fr' ,
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::POSTAL_CODE      => '44000' ,
        Schema::ADDRESS_LOCALITY => 'Nantes' ,
    ]),
]);
```

---

## Class catalog

| Category                | Classes                                                                                         |
|-------------------------|-------------------------------------------------------------------------------------------------|
| Incorporated entities   | `Corporation`, `Cooperative`, `Consortium`                                                       |
| Civil society           | `NGO`, `PoliticalParty`, `WorkersUnion`                                                          |
| Government              | `GovernmentOrganization`, `SearchRescueOrganization`                                             |
| Education               | `EducationalOrganization`, `LibrarySystem`                                                       |
| Health                  | `MedicalOrganization`                                                                            |
| Commerce                | `LocalBusiness`, `OnlineBusiness`                                                                |
| Performing & sport      | `PerformingGroup`, `SportsOrganization`                                                          |
| Research & funding      | `Project`, `ResearchOrganisation`, `FundingScheme`                                               |

For the exhaustive list, browse [`src/org/schema/organizations/`](../../../src/org/schema/organizations).

---

## Up to

[← `org\schema` overview](README.md)
