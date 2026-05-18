# Actions — `org\schema\actions`

Le namespace `org\schema\actions` implémente la **hiérarchie `Action` complète de Schema.org** (~115 classes). Une `Action` décrit une *action idéalisée* dans laquelle une ou plusieurs entités jouent les rôles `agent`, `participant`, `instrument`, `object` ou `result`.

> 🇬🇧 This page is also available in [English](../../en/schema-org/actions.md).

---

## Quand l'utiliser

Utilisez les actions chaque fois que vous voulez décrire **ce qui s'est passé** (ou ce qui devrait se passer) — en particulier pour :

- produire du balisage sémantique d'action compris par les moteurs de recherche et les assistants IA (`LikeAction`, `ListenAction`, `RegisterAction`, `BuyAction`, …),
- journaliser les interactions utilisateur de manière structurée,
- enrichir un système orienté événements avec un vocabulaire typé,
- décrire les actions potentielles sur une ressource via la propriété `potentialAction` de Schema.org.

La classe racine `Action` vit au sommet de `org\schema` ([types de base](core.md)) ; toutes les autres actions spécialisent l'une de ses branches.

---

## Exemple express

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

## Catalogue des classes par branche

| Branche                   | Exemples                                                                                        |
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
| `SearchAction`            | (classe unique — `query-input`)                                                                 |
| `SeekToAction`            | (déplacement média)                                                                             |
| `SolveMathAction`         | (classe unique)                                                                                 |
| `TradeAction`             | `BuyAction`, `DonateAction`, `OrderAction`, `PayAction`, `PreOrderAction`, `QuoteAction`, `RentAction`, `SellAction`, `TipAction` |
| `TransferAction`          | `BorrowAction`, `DownloadAction`, `GiveAction`, `LendAction`, `MoneyTransfer`, `ReceiveAction`, `ReturnAction`, `SendAction`, `TakeAction` |
| `UpdateAction`            | `AddAction` (`InsertAction` → `AppendAction`, `PrependAction`), `DeleteAction`, `ReplaceAction` |
| Authentification          | `AuthenticateAction`, `LoginAction`, `LogoutAction`                                              |

Pour la liste exhaustive, parcourez [`src/org/schema/actions/`](../../../src/org/schema/actions).

---

## Constantes

Les clés de propriétés liées aux actions (`agent`, `object`, `result`, `participant`, `instrument`, `actionStatus`, `target`, `startTime`, `endTime`, …) sont exposées par le trait `Action` sous [`org\schema\constants\traits/`](../../../src/org/schema/constants/traits/Action.php) et accessibles via [`Schema`](../../../src/org/schema/constants/Schema.php).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
