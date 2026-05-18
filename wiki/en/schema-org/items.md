# Items & lists — `org\schema\items`

The `org\schema\items` namespace gathers the **structured-list building blocks** used by `HowTo` content — steps, sections, items, supplies and tools.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/items.md).

---

## When to use it

Use these classes whenever you build a `HowTo` document (recipe, tutorial, instruction set) and need to expose its internal structure:

- group a multi-step procedure into `HowToSection`,
- describe each individual step with `HowToStep`,
- reference the items, supplies and tools required (`HowToItem`, `HowToSupply`, `HowToTool`).

The parent `HowTo` class itself lives in [creative-work](creative-work.md) — these classes are its building blocks.

For generic list semantics, prefer the top-level `ItemList` and `ListItem` classes documented in [core types](core.md).

---

## Quick example

```php
use org\schema\creativeWork\HowTo;
use org\schema\items\HowToStep;
use org\schema\items\HowToSupply;
use org\schema\items\HowToTool;
use org\schema\constants\Schema;

$howTo = new HowTo
([
    Schema::NAME    => 'Backup an OpenEdge database' ,
    Schema::SUPPLY  =>
    [
        new HowToSupply([ Schema::NAME => 'Free disk space (≥ 2× database size)' ]) ,
    ],
    Schema::TOOL    =>
    [
        new HowToTool([ Schema::NAME => 'probkup utility' ]) ,
    ],
    Schema::STEP    =>
    [
        new HowToStep
        ([
            Schema::NAME => 'Stop the brokers' ,
            Schema::TEXT => 'Run proshut <db> -by to gracefully stop all connections.' ,
        ]),
        new HowToStep
        ([
            Schema::NAME => 'Run an online backup' ,
            Schema::TEXT => 'Run probkup online <db> <backup-file>.' ,
        ]),
    ],
]);
```

---

## Class catalog

| Class           | Role                                                                |
|-----------------|---------------------------------------------------------------------|
| `HowToStep`     | A single step in the procedure.                                      |
| `HowToSection`  | A logical grouping of steps (chapter, phase, …).                     |
| `HowToItem`     | A consumable item produced or required by the procedure.             |
| `HowToSupply`   | A consumable supply required by the procedure.                        |
| `HowToTool`     | A reusable tool required by the procedure.                            |

For the exhaustive property set of each class, browse [`src/org/schema/items/`](../../../src/org/schema/items).

---

## Up to

[← `org\schema` overview](README.md)
