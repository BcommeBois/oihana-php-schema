# `com\progress\schema` — Catalogue système OpenEdge Progress SQL

Le namespace `com\progress\schema` est un **mapping orienté objet du catalogue système SQL d'OpenEdge Progress** (les tables `SYS%`). Il permet d'analyser, persister et émettre des charges utiles JSON-LD pour les tables, colonnes, index, vues, utilisateurs, privilèges, contraintes, séquences, synonymes, procédures, triggers et types de données — exactement comme le moteur Progress les décrit.

> 🇬🇧 This page is also available in [English](../en/openedge-progress.md).

---

## Quand l'utiliser

Utilisez ce namespace dès que vous ingérez, exposez ou documentez un schéma OpenEdge :

- importer les métadonnées d'une base Progress dans votre application,
- construire une UI d'administration au-dessus d'une instance OpenEdge,
- migrer un schéma entre Progress et un autre SGBDR,
- générer de la documentation ou des visualisations à partir de `SYSTABLES`, `SYSCOLUMNS`, …,
- décrire des droits et autorisations (`SYSDBAUTH`, `SYSTABAUTH`, `SYSCOLAUTH`).

Chaque entité expose le distinguisheur dédié `@context = 'https://schema.progress.com'` dans le JSON-LD, ce qui permet aux consommateurs de distinguer les métadonnées Progress des autres schémas.

---

## Exemple express

```php
use com\progress\schema\system\Table;
use com\progress\schema\system\Column;
use com\progress\schema\constants\Progress;

$table = new Table
([
    Progress::NAME    => 'Customer'    ,
    Progress::OWNER   => 'PUB'         ,
    Progress::CREATOR => 'sysprogress' ,
    Progress::TYPE    => 'T'           , // T = table utilisateur, V = vue, S = table système
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

## Catalogue des classes

Toutes les classes vivent sous [`com\progress\schema\system`](../../src/com/progress/schema/system) et étendent `org\schema\Thing`. La propriété héritée `name` contient toujours l'identifiant SQL de l'entrée du catalogue.

### Objets de schéma

| Classe      | Mappe vers     | Représente                                                                |
|-------------|----------------|---------------------------------------------------------------------------|
| `Table`     | `SYSTABLES`    | Une table utilisateur, une vue ou une table système. Porte propriétaire, type (`T`/`V`/`S`), créateur, statistiques. |
| `Column`    | `SYSCOLUMNS`   | Une colonne avec son `columnType` (type de données), largeur, précision, échelle, valeur par défaut, nullabilité, format/label. |
| `Index`     | `SYSINDEXES`   | Un couple `(index, colonne indexée)` — les index multi-colonnes sont représentés par plusieurs lignes ordonnées par `indexSequence`. |
| `View`      | `SYSVIEWS`     | Une définition de vue (`viewText`, `checkOption`, `textLength`).          |
| `Sequence`  | `SYSSEQUENCES` | Un générateur de séquence (`minValue`, `maxValue`, `increment`, `cycle`, `initialValue`). |
| `Synonym`   | `SYSSYNONYMS`  | Un synonyme pointant vers une table ou vue de base.                       |
| `Procedure` | `SYSPROCEDURES`| Une procédure stockée (`procedureId`, `numberOfArguments`, `returnType`, `procedureText`). |
| `Trigger`   | `SYSTRIGGER`   | Un trigger (`event` I/U/D, `timing` B/A, `forEach` R/S, `triggerText`).  |
| `DataType`  | `SYSDATATYPES` | Un type de données SQL supporté par le moteur (`typeCode`, `columnLength`, précision, radix). |

### Utilisateurs et privilèges

| Classe       | Mappe vers   | Représente                                                                |
|--------------|--------------|---------------------------------------------------------------------------|
| `User`       | `SYSDBAUTH`  | Un utilisateur OpenEdge SQL — la liste de facto des utilisateurs (créée par `GRANT DBA, RESOURCE TO …`). Expose `grantee`, `grantor`, `dbaAccess`, `resourceAccess`. |
| `TableAuth`  | `SYSTABAUTH` | Une ligne `(grantee, table)` portant les flags de privilèges niveau table : `select`, `insert`, `update`, `delete`, `references`, `index`, `alter`. |
| `ColumnAuth` | `SYSCOLAUTH` | Une ligne `(grantee, column)` portant les flags de privilèges niveau colonne : `select`, `update`, `references`. |

### Contraintes

| Classe                  | Mappe vers          | Représente                                                       |
|-------------------------|---------------------|------------------------------------------------------------------|
| `TableConstraint`       | `SYS_TBL_CONSTRS`   | En-tête de contrainte — `constraintType` (`P`/`U`/`F`/`C`), `deferrability`, statut. |
| `CheckConstraint`       | `SYS_CHK_CONSTRS`   | Expression SQL d'une contrainte `CHECK` (`checkText`).            |
| `ReferentialConstraint` | `SYS_REF_CONSTRS`   | Règles référentielles d'une clé étrangère — `matchType`, `updateRule`, `deleteRule`, clé unique cible. |
| `KeyColumnUsage`        | `SYS_KEYCOL_USAGE`  | Une entrée colonne-dans-clé, ordonnée par `keySequence`.          |

---

## Constantes de propriétés

Utilisez l'agrégateur [`com\progress\schema\constants\Progress`](../../src/com/progress/schema/constants/Progress.php). Il compose les traits par table sous [`com\progress\schema\constants\traits/`](../../src/com/progress/schema/constants/traits) (`Table`, `Column`, `Index`, `View`, `User`, `Sequence`, `Synonym`, `Procedure`, `Trigger`, `DataType`, `Authorization`, `Constraint`, `Common`).

```php
use com\progress\schema\constants\Progress;

Progress::SCHEMA;          // 'https://schema.progress.com'

// clés partagées
Progress::NAME;            // 'name'
Progress::OWNER;           // 'owner'
Progress::CREATOR;         // 'creator'
Progress::TYPE;            // 'type'

// clés spécifiques aux colonnes
Progress::COLUMN_TYPE;     // 'columnType'
Progress::NULL_FLAG;       // 'nullFlag'
Progress::WIDTH;           // 'width'

// clés d'autorisation (SYSTABAUTH / SYSCOLAUTH)
Progress::SELECT;          // 'select'
Progress::INSERT;          // 'insert'
Progress::ALTER;           // 'alter'
```

---

## Pour aller plus loin

- [`org\schema`](schema-org/README.md) — classe de base `Thing` étendue par chaque entité Progress.
- [Démarrage rapide](demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Tables du catalogue système Progress OpenEdge SQL (documentation officielle)](https://docs.progress.com/bundle/openedge-sql-reference/page/OpenEdge-SQL-system-catalog-tables.html).
- [Référence d'API](../../docs).
