# Organisations — `org\schema\organizations`

Le namespace `org\schema\organizations` contient les **~18 types d'organisations spécialisés** qui étendent `org\schema\Organization` — sociétés, ONG, organismes gouvernementaux, établissements éducatifs, bibliothèques, organisations sportives et médicales, et plus encore.

> 🇬🇧 This page is also available in [English](../../en/schema-org/organizations.md).

---

## Quand l'utiliser

Utilisez ces classes chaque fois qu'une `Organization` a besoin d'une identité plus précise :

- une `Corporation`, `Cooperative` ou `Consortium` pour les entités constituées en personne morale,
- une `NGO`, `PoliticalParty` ou `WorkersUnion` pour les organisations de la société civile,
- une `GovernmentOrganization` ou `SearchRescueOrganization` pour les organismes publics,
- une `EducationalOrganization`, `LibrarySystem` ou `MedicalOrganization` pour les institutions,
- une `LocalBusiness`, `OnlineBusiness` pour le commerce,
- une `FundingScheme`, `Project` ou `ResearchOrganisation` pour la recherche et les subventions.

---

## Exemple express

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

## Catalogue des classes

| Catégorie               | Classes                                                                                          |
|-------------------------|--------------------------------------------------------------------------------------------------|
| Personnes morales       | `Corporation`, `Cooperative`, `Consortium`                                                        |
| Société civile          | `NGO`, `PoliticalParty`, `WorkersUnion`                                                           |
| Gouvernement            | `GovernmentOrganization`, `SearchRescueOrganization`                                              |
| Éducation               | `EducationalOrganization`, `LibrarySystem`                                                        |
| Santé                   | `MedicalOrganization`                                                                             |
| Commerce                | `LocalBusiness`, `OnlineBusiness`                                                                 |
| Spectacle vivant & sport | `PerformingGroup`, `SportsOrganization`                                                          |
| Recherche & financement | `Project`, `ResearchOrganisation`, `FundingScheme`                                                |

Pour la liste exhaustive, parcourez [`src/org/schema/organizations/`](../../../src/org/schema/organizations).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
