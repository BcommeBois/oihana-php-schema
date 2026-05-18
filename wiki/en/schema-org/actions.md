# Actions — `org\schema\actions`

The `org\schema\actions` namespace implements the **full Schema.org `Action` hierarchy** (~115 classes). An `Action` describes an *idealized action* in which one or more entities play `agent`, `participant`, `instrument`, `object` or `result` roles.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/actions.md).

---

## When to use it

Use actions whenever you want to describe **what happened** (or what should happen) — in particular to:

- emit semantic action markup that search engines and AI assistants understand (`LikeAction`, `ListenAction`, `RegisterAction`, `BuyAction`, …),
- log user interactions in a structured way,
- back-fill an event-driven system with a typed vocabulary,
- describe potential actions on a resource through Schema.org's `potentialAction` property.

The root `Action` class lives at the top of `org\schema` ([core types](core.md)); every other action specialises one of its branches.

---

## Quick example

```php
use org\schema\actions\BuyAction;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;
use org\schema\constants\Schema;

$buy = new BuyAction
([
    Schema::AGENT     => new Person([ Schema::NAME => 'Alice' ]) ,
    Schema::OBJECT    => new Offer([ Schema::PRICE => '249.00' , Schema::PRICE_CURRENCY => 'EUR' ]) ,
    Schema::SELLER    => new Organization([ Schema::NAME => 'Oihana SAS' ]) ,
    Schema::START_TIME => '2026-05-18T10:00:00Z' ,
    Schema::END_TIME   => '2026-05-18T10:00:42Z' ,
]);
```

---

## Class catalog by branch

| Branch                    | Examples                                                                                        |
|---------------------------|-------------------------------------------------------------------------------------------------|
| `AchieveAction`           | `LoseAction`, `TieAction`, `WinAction`                                                          |
| `AssessAction`            | `ChooseAction`, `IgnoreAction`, `ReactAction` (`AgreeAction`, `DisagreeAction`, `LikeAction`, `DislikeAction`, `EndorseAction`, `WantAction`), `ReviewAction` |
| `ConsumeAction`           | `DrinkAction`, `EatAction`, `InstallAction`, `ListenAction`, `PlayGameAction`, `ReadAction`, `UseAction`, `ViewAction`, `WatchAction` |
| `ControlAction`           | `ActivateAction`, `DeactivateAction`, `ResumeAction`, `SuspendAction`                            |
| `CreateAction`            | `CookAction`, `DrawAction`, `FilmAction`, `PaintAction`, `PhotographAction`, `WriteAction`      |
| `FindAction`              | `CheckAction`, `DiscoverAction`, `TrackAction`                                                  |
| `InteractAction`          | `BefriendAction`, `CommunicateAction` (`AskAction`, `InviteAction`, `InformAction` (`ConfirmAction`, `RsvpAction`), `ReplyAction`, `ShareAction`), `FollowAction`, `JoinAction`, `LeaveAction`, `MarryAction`, `RegisterAction`, `SubscribeAction`, `UnRegisterAction` |
| `MoveAction`              | `ArriveAction`, `DepartAction`, `TravelAction`                                                  |
| `OrganizeAction`          | `AllocateAction` (`AcceptAction`, `AssignAction`, `AuthorizeAction`, `RejectAction`), `ApplyAction`, `BookmarkAction`, `PlanAction` (`CancelAction`, `ReserveAction`, `ScheduleAction`) |
| `PlayAction`              | `ExerciseAction`, `PerformAction`                                                               |
| `SearchAction`            | (single class — query `query-input`)                                                            |
| `SeekToAction`            | (media seek)                                                                                    |
| `SolveMathAction`         | (single class)                                                                                  |
| `TradeAction`             | `BuyAction`, `DonateAction`, `OrderAction`, `PayAction`, `PreOrderAction`, `QuoteAction`, `RentAction`, `SellAction`, `TipAction` |
| `TransferAction`          | `BorrowAction`, `DownloadAction`, `GiveAction`, `LendAction`, `MoneyTransfer`, `ReceiveAction`, `ReturnAction`, `SendAction`, `TakeAction` |
| `UpdateAction`            | `AddAction` (`InsertAction` → `AppendAction`, `PrependAction`), `DeleteAction`, `ReplaceAction` |
| Authentication            | `AuthenticateAction`, `LoginAction`, `LogoutAction`                                              |

For the exhaustive list, browse [`src/org/schema/actions/`](../../../src/org/schema/actions).

---

## Constants

Action-related property keys (`agent`, `object`, `result`, `participant`, `instrument`, `actionStatus`, `target`, `startTime`, `endTime`, …) are exposed by the `Action` trait under [`org\schema\constants\traits/`](../../../src/org/schema/constants/traits/Action.php) and reachable through [`Schema`](../../../src/org/schema/constants/Schema.php).

---

## Up to

[← `org\schema` overview](README.md)
