# `xyz\oihana\schema\statistics` — Oihana statistics

The `xyz\oihana\schema\statistics` namespace describes a **body of figures**: what someone — a customer, a supplier — traded over a period, measured from several angles at once.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/statistics.md).

---

## When to use it

Reach for these classes when you need to **store, serve or read back figures that have already been measured** — a yearly revenue and its monthly detail, a margin, volumes — rather than the documents that produced them:

- reporting a customer's or a supplier's performance year after year,
- feeding a sales dashboard (totals, trends, comparisons),
- keeping what a business management system computed on its own side, without recomputing it.

The records extend `org\schema\Intangible`: a reading of figures is neither a document nor a published file. They expose the `@context = 'https://schema.oihana.xyz'` discriminator in JSON-LD.

> ℹ️ **Why not `Dataset`?** A Schema.org `Dataset` is a **published body of data**: it gets catalogued, downloaded, and carries a publisher and a licence. A statistics record is a **reading** — one subject, one period — far closer to the `Observation` it is made of than to the corpus it may end up in.

---

## Quick example

```php
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\statistics\CustomerStatistics;
use xyz\oihana\schema\statistics\ObservationSeries;

$statistics = new CustomerStatistics
([
    CustomerStatistics::ABOUT              => '369980' ,          // the subject, by its reference
    CustomerStatistics::DIRECTION          => BusinessDocumentDirection::SALE ,
    CustomerStatistics::YEAR               => 2025 ,
    CustomerStatistics::OBSERVATION_PERIOD => 'P1M' ,             // twelve values per series
    CustomerStatistics::ASSIGNED_COMPANY   => '501' ,
    CustomerStatistics::ASSIGNED_SELLER    => 'JDOE' ,

    CustomerStatistics::REVENUE => new ObservationSeries
    ([
        Oihana::UNIT_CODE => 'EUR' ,
        Oihana::VALUE     => 271465.89 ,               // the year's total
        Oihana::VALUES    => [ 31545.48 , 32030.05 , 25604.00 , 37633.43 , 28802.74 , 24753.51 ,
                             23417.21 ,  7824.02 , 13665.69 , 17116.81 , 17232.02 , 11840.93 ] ,
    ]),

    CustomerStatistics::GROSS_MARGIN => new ObservationSeries
    ([
        Oihana::UNIT_CODE => 'EUR' ,
        Oihana::VALUE     => 66631.13 ,                // no monthly run: the source publishes none
    ]),
]);

echo json_encode( $statistics , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

And reading a stored document back, which retypes the whole tree at once:

```php
use oihana\reflect\Reflection;

$statistics = new Reflection()->hydrate( $document , CustomerStatistics::class );

$statistics->about;           // xyz\oihana\schema\organizations\Customer
$statistics->assignedSeller;  // org\schema\Person
$statistics->revenue;         // xyz\oihana\schema\statistics\ObservationSeries
$statistics->revenue->values[ 0 ] ;  // January
```

---

## The model, in three pieces

| Piece | What it states |
|---|---|
| **The record** (`Statistics`) | *whose* figures these are, on *which side* of the trade, over *which year*, at *which step* the series are cut, and for *which company*. **No figures.** |
| **The measure** (`ObservationSeries`) | *one* indicator, read over the whole period (`value`) and step by step (`values`), in its unit (`unitCode`). |
| **The ten measures** (`HasTradingMeasures`) | the trading indicator set, declared **once** and composed by every family. |

A **family** is a record whose subject is named: `CustomerStatistics` for a customer, `ProviderStatistics` for a supplier. It adds its own dimensions and nothing else — the rest is inherited or composed.

```
                       Statistics  (Intangible)
                       about · direction · year · observationPeriod · assignedCompany
                                    ▲
                 ┌──────────────────┴──────────────────┐
        CustomerStatistics                     ProviderStatistics
        + assignedSeller · assignedPOS         (no dimension of its own)

        both compose  HasTradingMeasures  → ten ObservationSeries
```

---

## Class catalogue

| Class                | Extends      | Role                                                                                                   |
|----------------------|--------------|--------------------------------------------------------------------------------------------------------|
| `Statistics`         | `Intangible` | The head of a record: subject, direction, year, series step, company. No measures.                      |
| `ObservationSeries`  | `org\schema\Observation` | One measure, read over the whole period and step by step.                                   |
| `CustomerStatistics` | `Statistics` | What a **customer** traded over one year, plus the salesperson and point of sale of the time.           |
| `ProviderStatistics` | `Statistics` | What was bought from a **supplier** over one year.                                                      |
| `CompanyStatistics`  | `Statistics` | What a **company** traded over one year — the branch manager's and the director's view.                 |
| `ProductStatistics`  | `Statistics` | What an **article** traded over one year, on the purchase side as on the sale side.                     |
| `SellerStatistics`   | `Statistics` | What a **salesperson** traded over one year, possibly customer by customer.                             |
| `SalesObjectives`    | `Statistics` | What a **salesperson** is aiming at over one year — the same measures, read as targets.                 |

### `Statistics` properties

| Property            | Type                                        | Description                                                                                          |
|---------------------|---------------------------------------------|------------------------------------------------------------------------------------------------------|
| `about`             | `int\|string\|array\|Thing\|null`            | The subject: the raw reference read from the source (a code), or the resolved object.                 |
| `direction`         | `string\|BusinessDocumentDirection\|null`    | Which side of the trade the figures were measured on — sale or purchase.                              |
| `year`              | `int\|null`                                  | The calendar year covered.                                                                            |
| `observationPeriod` | `string\|null`                               | The step the series are cut at, as an ISO 8601 duration — `P1M` for one value a month.                |
| `assignedCompany`   | `int\|string\|array\|Organization\|null`     | The company the figures were measured for.                                                            |

`id`, `name`, `url`, `additionalType`, `created` and `modified` are inherited from `Thing`.

### `ObservationSeries` properties

| Property   | Type                          | Description                                                                                     |
|------------|-------------------------------|-------------------------------------------------------------------------------------------------|
| `values`   | `array<int, int\|float>\|null` | The measure, one value per step of the period. Absent when the source publishes a total only.   |
| `value`    | `mixed`                        | The total over the period. *(inherited from `QuantitativeValue`)* Absent when unpublished.       |
| `unitCode` | `string\|null`                 | The unit — `MTQ`, `KGM`, or the ISO 4217 code of a monetary measure (`EUR`). *(inherited)*       |

`observationAbout`, `observationDate`, `marginOfError`, `measuredProperty` and the other `Observation` terms remain available; the record already carries them for all of its measures, so they are not repeated on each.

### The ten measures — `HasTradingMeasures`

| Measure          | What it measures                                                  | Total | Series | Confidential |
|------------------|-------------------------------------------------------------------|:-----:|:------:|:------------:|
| `revenue`        | What the trade earned — the amount invoiced.                      | ✅    | ✅     |              |
| `purchaseCost`   | The trade valued at the **price paid** for the goods.             | —     | ✅     | 🔒           |
| `averageCost`    | The trade valued at the **weighted average cost** of inventory.   | —     | ✅     | 🔒           |
| `costPrice`      | The trade valued at **cost price** (purchase plus charges).       | —     | ✅     | 🔒           |
| `purchaseMargin` | The margin over the purchase price.                               | ✅    | —      | 🔒           |
| `averageMargin`  | The margin over the weighted average cost.                        | ✅    | —      | 🔒           |
| `grossMargin`    | The margin over cost price — the **gross margin**.                | ✅    | —      | 🔒           |
| `quantity`       | How much was traded, in the units the items are counted in.       | —     | ✅     |              |
| `volume`         | The space the goods took up.                                      | —     | ✅     |              |
| `weight`         | What the goods weighed.                                           | —     | ✅     |              |

*(the “Total” and “Series” columns state what a source **usually** publishes — see the rule below)*

### `CustomerStatistics` properties

| Property         | Type                                     | Description                                                        |
|------------------|------------------------------------------|--------------------------------------------------------------------|
| `about`          | `#[HydrateAs(Customer::class)]`          | The customer — same union as on the record, subject named.         |
| `assignedSeller` | `int\|string\|array\|Person\|null`        | The salesperson the customer was attached to.                      |
| `assignedPOS`    | `int\|string\|array\|Warehouse\|null`     | The point of sale that served the customer.                        |

`ProviderStatistics` names its subject a `Provider` and adds no dimension: a supplier is not attached to a salesperson or to a point of sale the way a customer is.

### `CompanyStatistics` and `ProductStatistics` properties

Neither adds a property: they name their subject, and that is all. `CompanyStatistics` reads it as a `Company`, `ProductStatistics` as a `Product`.

🔑 **`CompanyStatistics` is the one family whose subject is the perimeter.** Elsewhere `assignedCompany` says which company a counterparty's figures were measured for; here that company **is** the subject, and the property stays unset rather than repeating it. A reader looking for the perimeter of any record therefore reads `about` first, and `assignedCompany` only when the two differ.

⚠️ **A group total and the sum of its members are not interchangeable.** A record about the whole group is a reading in its own right, published as such; adding it to its members' records counts every figure twice. The subject being typed at `Company` rather than at `Subsidiary`, the same property names a member of the group and the group itself — the stored `additionalType` is what tells a reader which is which.

🔑 **`ProductStatistics` is the one family where `quantity` is worth its total.** Elsewhere a quantity spanning several articles adds square metres to cubic metres to pieces; here every figure counts the same article in the same unit, and the run and its total both mean something. It carries no dimension of its own: what an article belongs to — its family, its category — lives on the article, and stays there.

### `SellerStatistics` and `SalesObjectives` properties

| Property           | Type                                          | Description                                                                   |
|--------------------|-----------------------------------------------|-------------------------------------------------------------------------------|
| `about`            | `#[HydrateAs(Seller::class)]`                 | The salesperson — same union as on the record, subject named.                 |
| `assignedCustomer` | `int\|string\|array\|Customer\|null`         | The customer the figure or the target is set on. Unset when the source totals the salesperson. |
| `assignedCategory` | `array\|string\|CategoryCode\|Thing\|null`   | *(`SalesObjectives` only)* The range of goods aimed at — a single code, or the ordered codes of a path through a classification, widest first. Unset when the target is set on a customer. |

**The two narrowings are alternatives**: a target is set on a customer **or** on a range of goods, never on both, and a target set on the salesperson alone leaves both unset.

**Both classes carry the same subject, and that is the whole point.** The outcome and the target line up key for key, with nothing to translate between them.

⚠️ **A target is rarely as detailed as it looks.** Sources commonly publish one measure — a revenue figure — and leave the nine others empty; and where a yearly target does carry a value per month, that detail is often the yearly figure spread over a seasonal curve rather than twelve decisions. None of this is visible in the record once written, so a reader who needs to know has to be told by whoever published it.

⚠️ **Attributing a figure to a salesperson is a choice, and two defensible ones disagree.** A source that attributes at the moment of the sale credits whoever made it, for good. A portfolio read from `CustomerStatistics::$assignedSeller` credits whoever holds the account *now*, and moves a whole history along with the account. Both are true sentences about different things, and they part company at every transfer.

---

## 🔑 The rule: a measure carries what its source states

A total with no monthly run, and a monthly run with no total, are **two ordinary cases**: a margin published once a year has no detail to give, a cost published month by month was never totalled.

**Neither absence is filled in by the library.** Once written, a total we summed and a monthly figure we derived look exactly like published ones: the reader has no way left to tell them apart. Summing, dividing, turning a margin into a rate — all of it belongs to whoever displays the figures, and stays visible there.

In practice, in a served document:

- `grossMargin` carries a `value` and **no** `values`;
- `purchaseCost` carries `values` and **no** `value`;
- a measure a source does not publish **does not appear at all** — rather than as a zero, which would state that nothing was traded.

## 🔒 Six of the ten measures are confidential by nature

The three costs and the three margins say what an operator earns on a given counterparty. They are exactly the figures a customer must **never** read about itself.

⚠️ **Hiding them from the projection is not enough.** Sorting on a margin, filtering by successive brackets, counting a facet or grouping by a cost reconstructs a value as surely as reading it. The library states the shape; **the guard belongs to the consuming application**, the only one that knows its readers — and it must cover all of those surfaces at once.

---

## A complete document

```json
{
  "@type": "CustomerStatistics",
  "@context": "https://schema.oihana.xyz",
  "id": "369980-sale-2025-501",
  "about": { "@type": "Customer", "id": "369980", "name": "Northwood Framing" },
  "direction": "https://schema.oihana.xyz/BusinessDocumentDirection#Sale",
  "year": 2025,
  "observationPeriod": "P1M",
  "assignedCompany": { "@type": "Subsidiary", "id": "501", "name": "Timber & Panels" },
  "assignedSeller": { "@type": "Person", "id": "JDOE", "name": "Jane Doe" },
  "assignedPOS": { "@type": "Warehouse", "id": "1", "name": "North yard" },
  "revenue": {
    "@type": "ObservationSeries", "unitCode": "EUR", "value": 271465.89,
    "values": [31545.48, 32030.05, 25604.00, 37633.43, 28802.74, 24753.51, 23417.21, 7824.02, 13665.69, 17116.81, 17232.02, 11840.93]
  },
  "costPrice": {
    "@type": "ObservationSeries", "unitCode": "EUR",
    "values": [24029.83, 23829.30, 18954.56, 27516.63, 21754.45, 18365.76, 17665.70, 6629.80, 10555.01, 13512.72, 12744.33, 9276.67]
  },
  "grossMargin": { "@type": "ObservationSeries", "unitCode": "EUR", "value": 66631.13 },
  "weight": {
    "@type": "ObservationSeries", "unitCode": "KGM",
    "values": [15561.29, 11705.11, 12023.16, 13984.03, 19368.78, 34424.62, 11401.22, 3875.47, 8373.95, 22811.65, 11937.06, 8512.40]
  },
  "modified": "2026-08-19T03:12:40+02:00"
}
```

---

## Related constants

Property keys are exposed by the [`StatisticsRecordTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/StatisticsRecordTrait.php), [`ObservationSeriesTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/ObservationSeriesTrait.php), [`HasTradingMeasuresTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/HasTradingMeasuresTrait.php), [`CustomerStatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/CustomerStatisticsTrait.php), [`SellerStatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/SellerStatisticsTrait.php) and [`SalesObjectivesTrait`](../../../src/xyz/oihana/schema/constants/traits/statistics/SalesObjectivesTrait.php) traits, composed in the [`StatisticsTrait`](../../../src/xyz/oihana/schema/constants/traits/StatisticsTrait.php) domain aggregator and wired into the [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php) master class. You can therefore reach them through `Oihana::YEAR`, `Oihana::REVENUE`, `Oihana::GROSS_MARGIN`, and each class exposes its own (`CustomerStatistics::REVENUE`).

---

## See also

- [Schema.org vocabulary](../schema-org/README.md) — `Observation`, `QuantitativeValue` and `StatisticalVariable`, the ground a measure stands on.
- [Business documents](business-documents.md) — the documents these figures come from, and the `BusinessDocumentDirection` enumeration they share.
- [Business entities](organizations.md) — `Customer`, `Provider` and `Company`, the subjects of three of these families.
