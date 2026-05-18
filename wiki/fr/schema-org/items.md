# Items & listes — `org\schema\items`

Le namespace `org\schema\items` rassemble les **briques de construction de listes structurées** utilisées par le contenu `HowTo` — étapes, sections, items, fournitures et outils.

> 🇬🇧 This page is also available in [English](../../en/schema-org/items.md).

---

## Quand l'utiliser

Utilisez ces classes chaque fois que vous construisez un document `HowTo` (recette, tutoriel, jeu d'instructions) et que vous voulez exposer sa structure interne :

- regrouper une procédure en plusieurs étapes via `HowToSection`,
- décrire chaque étape individuellement avec `HowToStep`,
- référencer les items, fournitures et outils nécessaires (`HowToItem`, `HowToSupply`, `HowToTool`).

La classe parente `HowTo` vit dans [creative-work](creative-work.md) — ces classes sont ses briques de construction.

Pour de la sémantique générique de liste, préférez les classes `ItemList` et `ListItem` de premier niveau documentées dans [types de base](core.md).

---

## Exemple express

```php
use org\schema\creativeWork\HowTo;
use org\schema\items\HowToStep;
use org\schema\items\HowToSupply;
use org\schema\items\HowToTool;
use org\schema\constants\Schema;

$howTo = new HowTo
([
    Schema::NAME    => 'Sauvegarder une base OpenEdge' ,
    Schema::SUPPLY  =>
    [
        new HowToSupply([ Schema::NAME => 'Espace disque libre (≥ 2× taille de la base)' ]) ,
    ],
    Schema::TOOL    =>
    [
        new HowToTool([ Schema::NAME => 'Utilitaire probkup' ]) ,
    ],
    Schema::STEP    =>
    [
        new HowToStep
        ([
            Schema::NAME => 'Arrêter les brokers' ,
            Schema::TEXT => 'Lancer proshut <db> -by pour arrêter proprement toutes les connexions.' ,
        ]),
        new HowToStep
        ([
            Schema::NAME => 'Lancer une sauvegarde en ligne' ,
            Schema::TEXT => 'Lancer probkup online <db> <fichier-sauvegarde>.' ,
        ]),
    ],
]);
```

---

## Catalogue des classes

| Classe          | Rôle                                                                 |
|-----------------|----------------------------------------------------------------------|
| `HowToStep`     | Une étape unique de la procédure.                                     |
| `HowToSection`  | Un regroupement logique d'étapes (chapitre, phase, …).                |
| `HowToItem`     | Un item consommable produit ou requis par la procédure.               |
| `HowToSupply`   | Une fourniture consommable requise par la procédure.                  |
| `HowToTool`     | Un outil réutilisable requis par la procédure.                        |

Pour le jeu exhaustif des propriétés de chaque classe, parcourez [`src/org/schema/items/`](../../../src/org/schema/items).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
