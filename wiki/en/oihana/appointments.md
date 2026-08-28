# `xyz\oihana\schema\appointments` — Appointments

The `xyz\oihana\schema\appointments` namespace describes a **meeting arranged with a customer** — on their premises, on a site, over the phone, over video — and **what gets written** once it has taken place.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/appointments.md).

---

## When to use it

Pick these classes when you need to **plan, retrieve or recount a meeting**:

- keep a salesperson's diary — what they have today, this week, this month;
- prepare a visit: who is being seen, where, and what one means to show them;
- record what was said, in what mood, and what is left to do;
- count — how many visits, how many landed, which ones need chasing.

An appointment extends `org\schema\Event`. That is what **every diary already reads**: its place, its hours, the people expected and the one whose diary it is are Schema.org's own properties, not house inventions. What the vocabulary has no word for is added beside them — the customer, the kind of meeting, what one means to present, the report.

> ℹ️ **Why not an `Action`?** An `Action` describes an agent acting on an object and producing a result — and a meeting looks like one. But it has **no word for a slot that moved**, where `Event` publishes `eventStatus` and `previousStartDate`; and it is the `Event` that diaries, iCalendar exports and interface components know how to read without being taught anything. The vocabulary of action is still used where it fits: the follow-up is one.

---

## Quick example

```php
use org\schema\constants\Schema;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;

$appointment = new CustomerAppointment
([
    Schema::NAME               => 'Product range review' ,
    Schema::START_DATE         => '2026-09-03T09:30:00+02:00' ,
    Schema::END_DATE           => '2026-09-03T10:30:00+02:00' ,
    Schema::DESCRIPTION        => 'Present the new range, chase the open quote.' ,

    Oihana::APPOINTMENT_TYPE   => 'VISIT' ,                       // a thesaurus term
    Oihana::APPOINTMENT_STATUS => AppointmentStatus::PLANNED ,
    Oihana::TAGS               => [ 'MEAL' , 'DEMO' ] ,        // the quick qualifiers

    Schema::ORGANIZER          => [ Schema::ID => 'JDOE' , Schema::NAME => 'Jane Doe' ] ,
    Schema::ABOUT              => [ Schema::ID => '100200' , Schema::NAME => 'Acme Corporation' ] ,
    Schema::ATTENDEE           => [ [ Schema::NAME => 'Alice Smith' ] ] ,
]);

echo json_encode( $appointment , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

And reading a stored document back, which retypes the whole tree at once:

```php
use oihana\reflect\Reflection;

$appointment = new Reflection()->hydrate( $document , CustomerAppointment::class );

$appointment->organizer;          // xyz\oihana\schema\auth\User
$appointment->about;              // xyz\oihana\schema\organizations\Customer
$appointment->attendee[ 0 ];      // xyz\oihana\schema\people\CustomerEmployee
$appointment->location;           // xyz\oihana\schema\places\CustomerSite
$appointment->report;             // xyz\oihana\schema\appointments\VisitReport
$appointment->report->followUp;   // xyz\oihana\schema\appointments\FollowUp[]
```

> ⚠️ **The constructor assigns raw, the hydration types.** `new CustomerAppointment([ … ])` copies what it is given as it is given; it is `Reflection::hydrate()` that reads the attributes and returns the nested objects in their class. Both paths are normal — the first writes, the second reads back.

---

## The model in three pieces

| Piece | What it states |
|---|---|
| **The meeting** (`Appointment`, and one class per family) | *with whom*, *when*, *where*, *of what kind*. Written **before**. |
| **The report** (`MeetingReport`, and `VisitReport` for a visit) | *what was said*, *who was actually there*, *what is still owed* — and, for a visit, *how it went* and *what it produced*. Written **after**. |
| **The follow-up** (`FollowUp`) | *what is still owed*, *by when*, and the meeting booked to honour it. |

```
             Appointment  (Event)
                     about · organizer · location · attendee · appointmentType
                     appointmentStatus · tags · assignedCompany
                              │
                              ├── CustomerAppointment   assignedSeller · makesOffer
                              └── InternalMeeting       (nothing more: about stays empty)
                              │
                              └── report ──▶ MeetingReport  (CreativeWork)
                                             topics · attendee · tags · text
                                                   │
                                                   └── VisitReport  (MeetingReport)
                                                       mood · outcome
                                                   │
                                                   └── followUp[] ──▶ FollowUp  (ScheduleAction)
                                                                      followUpType · scheduledTime
                                                                      actionStatus · result ──▶ CustomerAppointment
```

What is written **before** and what is written **after** sit side by side: `description`, `makesOffer` and `tags` are the preparation, `report` is what came of it. Neither overwrites the other — a meeting is worth reading precisely for the distance between the two.

---

## Class catalogue

| Class | Extends | Role |
|---|---|---|
| `Appointment` | `org\schema\Event` | A meeting arranged, of whatever kind. |
| `CustomerAppointment` | `Appointment` | A meeting arranged with a customer. |
| `InternalMeeting` | `Appointment` | A meeting between colleagues — with nobody from outside. |
| `MeetingReport` | `org\schema\CreativeWork` | What was written once the meeting took place. |
| `VisitReport` | `MeetingReport` | The report of a customer visit: the mood and the outcome on top. |
| `FollowUp` | `org\schema\actions\ScheduleAction` | What comes next, and when. |
| `AppointmentStatus` | `org\schema\enumerations\StatusEnumeration` | What became of the meeting itself. |
| `AppointmentCancelled`, `AppointmentDone`, `AppointmentNoShow`, `AppointmentPlanned` | `AppointmentStatus` | The member classes of the status — the object form, the one that carries a reason. |

---

## 🔑 Two axes of state, and they do not replace one another

This is the most important point on the page, and the most expensive one to discover along the way.

| Axis | Property | What it states | Values |
|---|---|---|---|
| **The slot** | `eventStatus` *(inherited from `Event`)* | what became of the **time** | `EventScheduled`, `EventRescheduled`, `EventPostponed`, `EventCancelled`, `EventMovedOnline` |
| **The meeting** | `appointmentStatus` | what became of the **meeting** | `Planned`, `Done`, `NoShow`, `Cancelled` |

Schema.org **publishes no member for "it happened"**: its enumeration follows an announced event, not a lived one. A diary reads the first axis — a moved meeting keeps its `previousStartDate` and is still to come; a report reads the second — a `Done` meeting no longer moves. One axis could not answer both questions.

🔑 **`NoShow` is not `Cancelled`.** A cancellation is announced and frees the time; an absence is discovered on the doorstep and costs the journey. Counting them together hides the only one worth acting on.

🔑 **And a status may state *why*.** Both axes accept the bare constant — `EventStatusType::CANCELLED` — or the **member class** when there is more to say: `new EventCancelled([ 'description' => 'The customer called off the day before.' ])`. Both spellings answer the same URI, so the choice is free; the second survives a round trip through storage because the union also accepts an array.

---

## `Appointment` properties

| Property | Type | Description |
|---|---|---|
| `about` | `string\|object\|array\|null` | **Whom** the meeting is with. No class is named at this level: each family narrows the union, and a meeting between colleagues leaves it empty. |
| `appointmentStatus` | `string\|array\|AppointmentStatus\|null` | What became of the meeting. Reads beside `eventStatus`. |
| `appointmentType` | `string\|array\|DefinedTerm\|null` | What kind of meeting it is — on the premises, over the phone, over video, on a site. One value. ⚠️ Not to be confused with the **family**, which the stored type carries. |
| `assignedCompany` | `string\|array\|Organization\|null` | The **company the meeting was arranged for** — the organizer's, frozen at creation. Read back as `Subsidiary`. It is there so a perimeter (« my branch's meetings ») is **a filter and not a walk**. |
| `attendee` | `Person\|Organization\|array\|null` | Who is **expected**. Optional, none or several. No class is named here either: who may be invited depends on whom one is meeting. |
| `organizer` | `Person\|Organization\|array\|null` | The **account** whose diary it is. Read back as `User`. 🔑 The account, never the business role: a person who gains a second role keeps **one diary**. |
| `report` | `array\|MeetingReport\|null` | The report. **One**, absent until there is something to report. |
| `tags` | `string[]\|DefinedTerm[]\|null` | The quick qualifiers — a meal, a tour of the premises, a site visit, a demonstration. Several. |

`name`, `description`, `startDate`, `endDate`, `duration`, `eventStatus`, `eventAttendanceMode`, `previousStartDate`, `remarks`, `subEvent`/`superEvent` are inherited from `Event`; `id`, `identifier`, `url`, `created`, `modified` from `Thing`.

🔑 **One property for the counterpart, whatever it points at.** A facet and a grouping aim at **one** property and one only: with a name per family, « how many meetings per counterpart » would have no answer at all. Same reason `Statistics::$about` carries the subject of every kind of figure.

## `CustomerAppointment` properties

| Property | Type | Description |
|---|---|---|
| `about` | `string\|object\|array\|null` | Redeclared to name who is meant: the customer, read back as `Customer`. A reference and its frozen copy, or a **free-form** customer. |
| `assignedSeller` | `int\|string\|array\|Person\|null` | The salesperson the customer is attached to. May differ from the organizer. |
| `attendee` | `Person\|Organization\|array\|null` | The customer's contacts **expected**. Read back as `CustomerEmployee`. |
| `location` | `PostalAddress\|Place\|VirtualLocation\|string\|array\|null` | Where it takes place: a customer address, a site, a virtual room. Read back as `CustomerSite`, `JobSite`, `Place`… |
| `makesOffer` | `Offer[]\|null` | What one means to present. See below. |
| `report` | `array\|MeetingReport\|null` | Redeclared to name the class that carries what a visit brings back: a `VisitReport`. |

🔑 **The declared type says `DefinedTerm`, the hydrator serves `ThesaurusTerm`.** `appointmentType` and `tags` come from **business** families — administered, not harvested — which carry properties `DefinedTerm` does not declare, `color` first among them. The union stays written `DefinedTerm` because it is the widest contract, and because `ThesaurusTerm` inherits from it: nothing that used to pass stops passing. But `hydrateCustomerAppointment()` now reads those terms back into the class the family actually serves, and its `$termClass` parameter lets a caller name another one — see [the hydrators](helpers.md).

### 🔑 What a meeting requires, and what its family requires

A meeting requires **a moment and a diary**. Whether it requires somebody on the other side is the **family's** business, not the parent class's: a customer meeting demands its customer, a meeting between colleagues demands nobody.

And the counterpart may take two forms, without the class having to tell them apart:

```php
// A known customer: a reference and its frozen copy.
Schema::ABOUT => [ '@type' => 'Customer' , '_key' => '137191259' , 'id' => '100200' , 'name' => 'Acme Corporation' ]

// A free-form customer: what is known of them, and no key.
Schema::ABOUT => [ 'name' => 'Acme Corporation' , 'telephone' => '05 56 00 00 00' ]
```

The library accepts both; it is up to the consumer to decide when the reference becomes mandatory, and how a free-form counterpart is later **attached** to a record created in the meantime.

### 🔑 `makesOffer` — an intention, not a quote

What a salesperson means to put in front of their customer is written as **offers**, one per product:

```php
Schema::MAKES_OFFER =>
[
    [
        Schema::DESCRIPTION  => 'Show them model A rather than B.' ,
        Schema::ITEM_OFFERED => [ 'id' => '500100' , 'name' => 'Model A widget' ,
                                  'image' => [ '@type' => 'ImageObject' , 'contentUrl' => 'https://example.org/model-a.jpg' ] ] ,
    ],
]
```

The wrapper is what carries the intention **beside** the reference — and the day an intention becomes a figure (a discount considered, a quantity worth quoting), `Offer` already has `price`, `priceSpecification` and `eligibleQuantity`: no property to invent. The name and the meaning are the ones `Organization::$makesOffer` already carries.

⚠️ **Nothing here commits anyone.** The document that does is written elsewhere, and points back at the meeting it came from (`Thing::$subjectOf`).

---

## `MeetingReport` properties

| Property | Type | Description |
|---|---|---|
| `attendee` | `array\|Person\|null` | Who was **actually** there. No class is named here: who sits at a table depends on the kind of meeting, and each family narrows the union. |
| `followUp` | `FollowUp[]\|null` | What is left to do. None, one or several. |
| `tags` | `string[]\|DefinedTerm[]\|null` | Qualifiers of the report itself. Declared for the day one is needed — a meeting's qualifiers live on the meeting. |
| `topics` | `string[]\|DefinedTerm[]\|null` | What was discussed. Several. |

`text` (the body of the report), `author`, `dateCreated`, `dateModified`, `audio` and `associatedMedia` are inherited from `CreativeWork`.

🔑 **What a report stores has nothing to do with who the meeting was with.** A text, its promises, what was covered, who came: an appointment of any kind writes the same things afterwards. That is why this class sits **above** `VisitReport` rather than beside it.

## `VisitReport` properties

| Property | Type | Description |
|---|---|---|
| `attendee` | `array\|Person\|null` | Redeclared to name who is meant: read back as `CustomerEmployee`. |
| `mood` | `string\|array\|DefinedTerm\|null` | How the meeting felt — satisfied, neutral, a problem to deal with. One value. |
| `outcome` | `string\|array\|DefinedTerm\|null` | What the meeting produced — an order, a quote to write, something to chase, nothing. One value. |

🔑 **Those two are what a sales review reads**, and they are the reason this class exists: « how many visits produced an order » is a question no free text can answer.

🔑 **The same holds for the four vocabularies of a visit report.** `mood`, `outcome`, `tags` and `topics` are declared `DefinedTerm` and served as `ThesaurusTerm` by `hydrateVisitReport()`. That is what makes a term read inside a report and the same term read on its own family **answer the same shape**, `color` included: only the second one carried it before.

🔑 **The boxes and the text are not alternatives.** A report reduced to codes loses what makes it worth reading; reduced to prose, it cannot be counted. Both are declared, **neither is required**: the one written on a phone, in a van, with three taps is worth more than the thorough one that never gets written.

🔑 **The mood is not the outcome.** A meeting can go well and produce nothing, and a tense one can end in an order. Read together they say something neither says alone.

⚠️ **The report's attendees are not the meeting's.** One states who was expected, the other who came. They disagree often enough that they must not be conflated: folding them together would quietly rewrite a plan into a record of fact.

---

## `FollowUp` properties

| Property | Type | Description |
|---|---|---|
| `followUpType` | `string\|array\|DefinedTerm\|null` | The kind of next step — call back, send the quote, visit again. |
| `result` | `Thing\|array\|string\|null` | The meeting **booked** to honour it, when there is one. Read back as `CustomerAppointment`. |

`scheduledTime` (when it is due), `actionStatus` (still owed, or honoured), `agent` (who owes it), `name` and `description` are inherited from `ScheduleAction` and `Action`.

🔑 **A promise is not an appointment.** "Call them back in a fortnight" is owed by someone and has no slot. Writing the promise as a meeting would put a placeholder in a diary and lose the distinction between **what is agreed** and **what is booked**. The day the meeting is booked, it is named in `result` — and the promise moves to `CompletedActionStatus`.

---

## A salesperson's availability

It does not live here but on the **account** — [`User::$hoursAvailable`](auth.md) — because it depends on no meeting: it is the rhythm meetings come and sit in. And on the account rather than on a business role, because a person gaining a second role keeps **one diary** and **one set of hours**.

```php
use xyz\oihana\schema\auth\User;

$user = new User
([
    'name'           => 'Jane Doe' ,
    'hoursAvailable' =>
    [
        [ 'dayOfWeek' => [ 'Monday' , 'Tuesday' , 'Thursday' ] , 'opens' => '08:30' , 'closes' => '18:00' ] ,
        [ 'dayOfWeek' => 'Friday' , 'opens' => '08:30' , 'closes' => '12:00' ] ,
        [ 'validFrom' => '2026-08-10' , 'validThrough' => '2026-08-21' ] ,   // a closure: neither opens nor closes
    ],
]);
```

🔑 **Silence is not an opening.** Whoever offers a slot needs a **positive** statement of when it may be offered; saying nothing means no slot can be proposed — the safe reading rather than the permissive one.

---

## The whole document

```json
{
  "@type": "CustomerAppointment",
  "@context": "https://schema.oihana.xyz",
  "name": "Product range review",
  "startDate": "2026-09-03T09:30:00+02:00",
  "endDate": "2026-09-03T10:30:00+02:00",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "appointmentStatus": "https://schema.oihana.xyz/AppointmentStatus#Done",
  "appointmentType": "VISIT",
  "tags": ["MEAL", "DEMO"],
  "organizer": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
  "assignedSeller": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
  "about": { "@type": "Customer", "id": "100200", "name": "Acme Corporation" },
  "attendee": [ { "@type": "CustomerEmployee", "id": "55", "name": "Alice Smith", "jobTitle": "ACH" } ],
  "location": {
    "@type": "CustomerSite", "name": "Head office",
    "address": { "@type": "PostalAddress", "streetAddress": "1 Example Street", "postalCode": "10000", "addressLocality": "Exampleville" },
    "geo": { "@type": "GeoCoordinates", "latitude": 44.84, "longitude": -0.64 }
  },
  "description": "Present the new range, chase the open quote.",
  "makesOffer": [
    {
      "@type": "Offer",
      "description": "Show them model A rather than B; a discount is possible above a hundred units.",
      "itemOffered": { "@type": "Product", "id": "500100", "name": "Model A widget" }
    }
  ],
  "report": {
    "@type": "VisitReport",
    "text": "Range well received. They want a price for a hundred units delivered.",
    "outcome": "QUOTE",
    "mood": "GREEN",
    "topics": ["PRICING", "DELIVERY"],
    "attendee": [ { "@type": "CustomerEmployee", "id": "55", "name": "Alice Smith" } ],
    "followUp": [
      {
        "@type": "FollowUp",
        "followUpType": "CALL",
        "scheduledTime": "2026-09-10",
        "description": "Call back once the quote has been sent.",
        "actionStatus": "https://schema.org/PotentialActionStatus"
      }
    ],
    "author": { "@type": "Seller", "id": "JDOE", "name": "Jane Doe" },
    "dateCreated": "2026-09-03T10:41:00+02:00"
  },
  "created": "2026-08-19T10:02:00+02:00",
  "modified": "2026-09-03T10:41:00+02:00"
}
```

---

## Reading an appointment back

A consumer rarely reads a stored meeting back through reflection : they hold a row and write `new CustomerAppointment( $row )`. But **a constructor assigns flat** — the `#[HydrateAs]` / `#[HydrateWith]` attributes the classes carry are honored only by `Reflection::hydrate()`, a path it does not take. The `report` stays an array, so does every contact, and a status written in its object form comes back a raw array.

Four helpers cover the namespace, on the model of the library's `hydrateXxx()` family :

| Function | Answers | Nested references hydrated |
|---|---|---|
| [`hydrateCustomerAppointment()`](helpers.md#xyzoihanaschemahelpershydrateappointments--the-appointment-hydrators) | `CustomerAppointment` or a list | `customer`, `attendee`, `assignedSeller`, `appointmentType`, `tags`, `makesOffer`, `report`, `eventStatus`, `appointmentStatus` |
| [`hydrateVisitReport()`](helpers.md#xyzoihanaschemahelpershydrateappointments--the-appointment-hydrators) | `VisitReport` or a list | `attendee`, `followUp`, `mood`, `outcome`, `tags`, `topics`, `author` |
| [`hydrateFollowUp()`](helpers.md#xyzoihanaschemahelpershydrateappointments--the-appointment-hydrators) | `FollowUp` or a list | `followUpType`, `agent`, `result` |
| [`hydrateAppointmentStatus()`](helpers.md#xyzoihanaschemahelpershydrateappointments--the-appointment-hydrators) | the member class of `AppointmentStatus` | — |

```php
use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateCustomerAppointment;

$appointment = hydrateCustomerAppointment( $row ) ;

$appointment->about                        instanceof Customer         ; // true
$appointment->attendee[ 0 ]->workLocation  instanceof CustomerSite     ; // true
$appointment->report->followUp[ 0 ]        instanceof FollowUp         ; // true
$appointment->appointmentStatus            instanceof AppointmentDone  ; // true
```

One call is enough : the head goes through `Reflection::hydrate()`, then re-reads from the raw payload whatever reflection cannot settle, or types less deeply than the helper does. Anything that is not an array — an unresolved string reference, an already typed instance — comes back untouched, **entry by entry inside a list as much as on its own**, and a list whose every entry was an array that gave nothing answers `null` rather than a leftover raw array.

🚨 **The meeting a follow-up names is not unfolded.** `FollowUp::$result` names a meeting, whose report carries follow-ups, which name meetings in turn : going down through the deep helper opens a cycle only the data would stop. It is a **reference**, typed one level and no further — what it carries itself stays raw, and whoever needs it asks for that meeting on its own.

🔑 **An empty follow-up list stays an empty list**, where the rest of the family answers `null`. « This report has no follow-up » is an answer, and it is not the answer « nothing here was readable » : a reader walking the value deserves a list to walk.

🔑 **Three properties are left to their attribute**, on purpose. `organizer`, `assignedCompany` and `location` are already settled exactly by reflection, from the payload's `@type`. Forcing a class over them would do worse than nothing : a plain organization read back as a subsidiary, a virtual room as a customer site. `assignedSeller` is the only one that needs the helper's hand — it declares no attribute and its union names a plain `Person`, where the property means a `Seller`.

### A status is read back, and it is filtered

Both axes accept the bare constant or the member class. Measured : the object form serializes as `{"@type":"AppointmentCancelled", …}` — the `@type` carries the **short name**, never the URI. Left there, the two spellings compare to nothing in common, and a store can no longer filter « the cancelled meetings » without giving up one of them.

Two answers, and no new property :

- **the member states its own URI** — built, it fills in the `additionalType` that `Thing` already declares, with the URI the bare constant carries. `??=` : a caller stays free to impose something else, and a value read back from a store is never overwritten ;
- **`hydrateAppointmentStatus()` and [`hydrateEventStatus()`](helpers.md#orgschemahelpershydrate--the-pure-hydrators)** read that URI back — then, failing that, the `@type` — and answer the right member class. The bare string comes back untouched, an array becomes the object carrying its reason again.

```php
new AppointmentCancelled([ 'description' => 'The customer called it off the day before.' ])->additionalType
    === AppointmentStatus::CANCELLED ; // true, before and after a JSON round trip
```

⚠️ **The two never coincide on this side** : the vocabulary spells `…/AppointmentStatus#NoShow` where the class is named `AppointmentNoShow`. No rule takes one to the other — which is precisely why the URI is **stated** by the member rather than derived from its name.

---

## Related constants

The property keys are exposed by the [`CustomerAppointmentTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/CustomerAppointmentTrait.php), [`VisitReportTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/VisitReportTrait.php) and [`FollowUpTrait`](../../../src/xyz/oihana/schema/constants/traits/appointments/FollowUpTrait.php) traits, composed into the domain aggregator [`AppointmentsTrait`](../../../src/xyz/oihana/schema/constants/traits/AppointmentsTrait.php) and wired into the master class [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php). You can therefore reach them through `Oihana::APPOINTMENT_TYPE`, `Oihana::MOOD`, `Oihana::FOLLOW_UP`, and so on — and each class exposes its own (`CustomerAppointment::REPORT`).

The properties borrowed from Schema.org — `about`, `attendee`, `location`, `organizer`, `makesOffer`, `assignedSeller`, `scheduledTime` — keep their existing constants: they are not redeclared.

---

## See also

- [Schema.org vocabulary](../schema-org/README.md) — `Event`, `CreativeWork`, `Action` and `OpeningHoursSpecification`, the foundation it all rests on.
- [Authentication](auth.md) — `User`, the account whose diary holds the meetings, and its availability.
- [People](people.md) — `Seller`, `CustomerEmployee` for the contacts met.
- [Business entities](organizations.md) — `Customer`, the subject of every meeting.
- [Places](places.md) — `CustomerSite` and `JobSite`, where meetings take place.
- [Business documents](business-documents.md) — the quotes and orders a meeting gives rise to.
