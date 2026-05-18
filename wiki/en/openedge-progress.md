# `com\progress\schema` — OpenEdge Progress SQL catalog

The `com\progress\schema` namespace is an **object-oriented mapping of the OpenEdge Progress SQL system catalog** (the `SYS%` tables). It lets you parse, persist and emit JSON-LD payloads for database tables, columns, indexes, views, users, privileges, constraints, sequences, synonyms, procedures, triggers and data types — exactly as the Progress engine describes them.

> 🇫🇷 Cette page existe aussi en [français](../fr/openedge-progress.md).

---

## When to use it

Use this namespace whenever you ingest, expose or document an OpenEdge schema:

- importing the metadata of a Progress database into your application,
- building an admin UI on top of an OpenEdge instance,
- migrating a schema between Progress and another RDBMS,
- generating documentation or visualisations from `SYSTABLES`, `SYSCOLUMNS`, …,
- describing grants and authorizations (`SYSDBAUTH`, `SYSTABAUTH`, `SYSCOLAUTH`).

Every entity exposes the dedicated `@context = 'https://schema.progress.com'` distinguisher in the JSON-LD output, so consumers can tell Progress metadata apart from other schemas.

---

## Quick example

```php
use com\progress\schema\system\Table;
use com\progress\schema\system\Column;
use com\progress\schema\constants\Progress;

$table = new Table
([
    Progress::NAME    => 'Customer'    ,
    Progress::OWNER   => 'PUB'         ,
    Progress::CREATOR => 'sysprogress' ,
    Progress::TYPE    => 'T'           , // T = user table, V = view, S = system
    Progress::COLUMNS =>
    [
        new Column
        ([
            Progress::NAME          => 'CustNum'  ,
            Progress::COLUMN_ID     => 1          ,
            Progress::COLUMN_TYPE   => 'INTEGER'  ,
            Progress::NULL_FLAG     => false      ,
            Progress::WIDTH         => 4          ,
            Progress::PRECISION     => 10         ,
        ]),
        new Column
        ([
            Progress::NAME          => 'Name' ,
            Progress::COLUMN_ID     => 2     ,
            Progress::COLUMN_TYPE   => 'VARCHAR' ,
            Progress::WIDTH         => 100 ,
        ]),
    ],
]);

echo json_encode( $table , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Class catalog

Every class lives under [`com\progress\schema\system`](../../src/com/progress/schema/system) and extends `org\schema\Thing`. The inherited `name` property always holds the SQL identifier of the catalog entry.

### Schema objects

| Class       | Maps to       | What it represents                                                        |
|-------------|---------------|---------------------------------------------------------------------------|
| `Table`     | `SYSTABLES`   | A user table, view or system table. Carries owner, type (`T`/`V`/`S`), creator, statistics. |
| `Column`    | `SYSCOLUMNS`  | A column with its `columnType` (data type), width, precision, scale, default value, nullability, format/label. |
| `Index`     | `SYSINDEXES`  | One `(index, indexed-column)` pair — multi-column indexes are represented by several rows ordered by `indexSequence`. |
| `View`      | `SYSVIEWS`    | A view definition (`viewText`, `checkOption`, `textLength`).              |
| `Sequence`  | `SYSSEQUENCES`| A sequence generator (`minValue`, `maxValue`, `increment`, `cycle`, `initialValue`). |
| `Synonym`   | `SYSSYNONYMS` | A synonym pointing to a base table or view.                                |
| `Procedure` | `SYSPROCEDURES` | A stored procedure (`procedureId`, `numberOfArguments`, `returnType`, `procedureText`). |
| `Trigger`   | `SYSTRIGGER`  | A trigger (`event` I/U/D, `timing` B/A, `forEach` R/S, `triggerText`).    |
| `DataType`  | `SYSDATATYPES`| A SQL data type supported by the engine (`typeCode`, `columnLength`, precision, radix). |

### Users and privileges

| Class        | Maps to       | What it represents                                                       |
|--------------|---------------|--------------------------------------------------------------------------|
| `User`       | `SYSDBAUTH`   | An OpenEdge SQL user — the de facto user list (created by `GRANT DBA, RESOURCE TO …`). Exposes `grantee`, `grantor`, `dbaAccess`, `resourceAccess`. |
| `TableAuth`  | `SYSTABAUTH`  | A `(grantee, table)` row carrying the table-level privilege flags: `select`, `insert`, `update`, `delete`, `references`, `index`, `alter`. |
| `ColumnAuth` | `SYSCOLAUTH`  | A `(grantee, column)` row carrying the column-level privilege flags: `select`, `update`, `references`. |

### Constraints

| Class                   | Maps to            | What it represents                                                |
|-------------------------|--------------------|-------------------------------------------------------------------|
| `TableConstraint`       | `SYS_TBL_CONSTRS`  | Constraint header — `constraintType` (`P`/`U`/`F`/`C`), `deferrability`, status. |
| `CheckConstraint`       | `SYS_CHK_CONSTRS`  | SQL expression of a `CHECK` constraint (`checkText`).             |
| `ReferentialConstraint` | `SYS_REF_CONSTRS`  | Foreign-key referential rules — `matchType`, `updateRule`, `deleteRule`, target unique key. |
| `KeyColumnUsage`        | `SYS_KEYCOL_USAGE` | One column-in-a-key entry, ordered by `keySequence`.              |

---

## Property constants

Use the aggregator [`com\progress\schema\constants\Progress`](../../src/com/progress/schema/constants/Progress.php). It composes the per-table traits under [`com\progress\schema\constants\traits/`](../../src/com/progress/schema/constants/traits) (`Table`, `Column`, `Index`, `View`, `User`, `Sequence`, `Synonym`, `Procedure`, `Trigger`, `DataType`, `Authorization`, `Constraint`, `Common`).

```php
use com\progress\schema\constants\Progress;

Progress::SCHEMA;          // 'https://schema.progress.com'

// shared keys
Progress::NAME;            // 'name'
Progress::OWNER;           // 'owner'
Progress::CREATOR;         // 'creator'
Progress::TYPE;            // 'type'

// column-specific keys
Progress::COLUMN_TYPE;     // 'columnType'
Progress::NULL_FLAG;       // 'nullFlag'
Progress::WIDTH;           // 'width'

// authorization keys (SYSTABAUTH / SYSCOLAUTH)
Progress::SELECT;          // 'select'
Progress::INSERT;          // 'insert'
Progress::ALTER;           // 'alter'
```

---

## Related reading

- [`org\schema`](schema-org/README.md) — base `Thing` class that every Progress entity extends.
- [Getting started](getting-started.md) — installation, hydration, JSON-LD basics.
- [Progress OpenEdge SQL system catalog tables (official documentation)](https://docs.progress.com/bundle/openedge-sql-reference/page/OpenEdge-SQL-system-catalog-tables.html).
- [API reference](../../docs).
