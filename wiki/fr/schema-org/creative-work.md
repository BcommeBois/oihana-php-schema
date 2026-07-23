# Œuvres créatives — `org\schema\creativeWork`

Le namespace `org\schema\creativeWork` contient les **~60 types d'œuvres créatives spécialisés** qui étendent `org\schema\CreativeWork` — contenu écrit, enregistré ou éditorialisé décrit avec la sémantique de Schema.org.

> 🇬🇧 This page is also available in [English](../../en/schema-org/creative-work.md).

---

## Quand l'utiliser

Utilisez ce namespace chaque fois que vous décrivez un contenu plutôt qu'une action, un lieu ou un objet commercial — par exemple :

- contenu éditorial (`Article`, `Book`, `Chapter`),
- objets média (`ImageObject`, `VideoObject`, `AudioObject`, `Photograph`, `Drawing`),
- données structurées (`Dataset`, `DataCatalog`, `DataFeed`),
- matériel pédagogique et accréditations (`HowTo`, `Certification`, `Credential`, `EducationalOccupationalCredential`),
- pages web et structure de site (`WebPage`, `WebSite`, `BreadcrumbList`, `SiteNavigationElement`),
- contenu conversationnel (`Comment`, `Question`, `Answer`, `Quotation`).

---

## Exemple express

```php
use org\schema\creativeWork\Article;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\Organization;
use org\schema\Person;
use org\schema\constants\Schema;

$article = new Article
([
    Schema::HEADLINE      => 'Modéliser les métadonnées OpenEdge avec Oihana PHP Schema' ,
    Schema::AUTHOR        => new Person([ Schema::NAME => 'Marc Alcaraz' ]) ,
    Schema::PUBLISHER     => new Organization([ Schema::NAME => 'Oihana SAS' ]) ,
    Schema::DATE_PUBLISHED => '2026-05-18' ,
    Schema::IMAGE         => new ImageObject
    ([
        Schema::URL    => 'https://example.com/cover.jpg' ,
        Schema::WIDTH  => 1200 ,
        Schema::HEIGHT => 630 ,
    ]),
]);
```

---

## Catalogue des classes (points clés)

| Catégorie              | Classes                                                                                          |
|------------------------|--------------------------------------------------------------------------------------------------|
| Éditorial              | `Article`, `Book`, `Chapter`, `CreativeWorkSeries`, `Collection`, `HowTo`, `Map`, `Atlas`         |
| Média                  | `MediaObject`, `ImageObject`, `VideoObject`, `AudioObject`, `Photograph`, `Drawing`              |
| Données                | `Dataset`, `DataCatalog`, `DataFeed`, `DataDownload`, `Table`                                    |
| Documents              | `DigitalDocument`, `DigitalDocumentPermission`, `Quotation`                                       |
| Conversationnel        | `Comment`, `CorrectionComment`, `Question`, `Answer`                                              |
| Pages web              | `WebPage`, `AboutPage`, `ContactPage`, `CheckoutPage`, `FAQPage`, `QAPage`, `ItemPage`, `SearchResultsPage`, `MedicalWebPage`, `WebContent` |
| Structure web          | `WebSite`, `BreadcrumbList`, `SiteNavigationElement`, `WPHeader`, `WPFooter`, `WPSideBar`, `WPAdBlock` |
| Logiciel & applications | `SoftwareApplication`                                                                            |
| Connaissances & termes | `DefinedTermSet`, `CategoryCodeSet`, `Claim`, `SpeakableSpecification`                            |
| Accréditations         | `Certification`, `Credential`, `EducationalOccupationalCredential`                              |
| Immobilier             | `RealEstateListing`                                                                              |

Des sous-dossiers organisent les spécialisations : [`creativeWork/medias/`](../../../src/org/schema/creativeWork/medias) (ImageObject, VideoObject, AudioObject), [`creativeWork/comments/`](../../../src/org/schema/creativeWork/comments) (Answer, CorrectionComment), [`creativeWork/web/`](../../../src/org/schema/creativeWork/web) (WPHeader, WPFooter, …).

Pour la liste exhaustive, parcourez [`src/org/schema/creativeWork/`](../../../src/org/schema/creativeWork).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
