# Creative works — `org\schema\creativeWork`

The `org\schema\creativeWork` namespace holds the **~60 specialised creative-work types** that extend `org\schema\CreativeWork` — written, recorded or curated content described with Schema.org semantics.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/creative-work.md).

---

## When to use it

Use this namespace whenever you describe a piece of content rather than an action, a place or a commerce object — for instance:

- editorial content (`Article`, `Book`, `Chapter`),
- media objects (`ImageObject`, `VideoObject`, `AudioObject`, `Photograph`, `Drawing`),
- structured data (`Dataset`, `DataCatalog`, `DataFeed`),
- learning material and credentials (`HowTo`, `Certification`, `EducationalOccupationalCredential`),
- web pages and site structure (`WebPage`, `WebSite`, `BreadcrumbList`, `SiteNavigationElement`),
- conversational content (`Comment`, `Question`, `Answer`, `Quotation`).

---

## Quick example

```php
use org\schema\creativeWork\Article;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\Organization;
use org\schema\Person;
use org\schema\constants\Schema;

$article = new Article
([
    Schema::HEADLINE      => 'Modeling OpenEdge metadata with Oihana PHP Schema' ,
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

## Class catalog (highlights)

| Category               | Classes                                                                                          |
|------------------------|--------------------------------------------------------------------------------------------------|
| Editorial              | `Article`, `Book`, `Chapter`, `CreativeWorkSeries`, `Collection`, `HowTo`, `Map`, `Atlas`         |
| Media                  | `MediaObject`, `ImageObject`, `VideoObject`, `AudioObject`, `Photograph`, `Drawing`              |
| Data                   | `Dataset`, `DataCatalog`, `DataFeed`, `DataDownload`, `Table`                                    |
| Documents              | `DigitalDocument`, `DigitalDocumentPermission`, `Quotation`                                       |
| Conversational         | `Comment`, `CorrectionComment`, `Question`, `Answer`                                              |
| Web pages              | `WebPage`, `AboutPage`, `ContactPage`, `CheckoutPage`, `FAQPage`, `QAPage`, `ItemPage`, `SearchResultsPage`, `MedicalWebPage`, `WebContent` |
| Web structure          | `WebSite`, `BreadcrumbList`, `SiteNavigationElement`, `WPHeader`, `WPFooter`, `WPSideBar`, `WPAdBlock` |
| Software & apps        | `SoftwareApplication`                                                                            |
| Knowledge & terms      | `DefinedTermSet`, `CategoryCodeSet`, `Claim`, `SpeakableSpecification`                            |
| Credentials            | `Certification`, `EducationalOccupationalCredential`                                              |
| Real-estate            | `RealEstateListing`                                                                              |

Sub-folders organise specialisations: [`creativeWork/medias/`](../../../src/org/schema/creativeWork/medias) (ImageObject, VideoObject, AudioObject), [`creativeWork/comments/`](../../../src/org/schema/creativeWork/comments) (Answer, CorrectionComment), [`creativeWork/web/`](../../../src/org/schema/creativeWork/web) (WPHeader, WPFooter, …).

For the exhaustive list, browse [`src/org/schema/creativeWork/`](../../../src/org/schema/creativeWork).

---

## Up to

[← `org\schema` overview](README.md)
