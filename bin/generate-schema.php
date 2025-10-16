<?php

declare(strict_types=1);

// Simple JSON Schema generator from PHP typed public properties
// Usage: php bin/generate-schema.php org\\schema\\Place

require __DIR__ . '/../vendor/autoload.php';

/**
 * Map a reflection named type to JSON Schema fragment and collect $defs for class types.
 * @throws ReflectionException
 */
function mapTypeToJsonSchema(ReflectionNamedType $type, array &$defs): array
{
    $name = $type->getName();
    $isBuiltin = $type->isBuiltin();

    if ($isBuiltin) {
        return match ($name) {
            // 'string' => [ 'type' => 'string' ], // default ?
            'int'    => [ 'type' => 'integer' ],
            'float'  => [ 'type' => 'number' ],
            'bool'   => [ 'type' => 'boolean' ],
            'array'  => [ 'type' => 'array' ],
            'object' => [ 'type' => 'object' ],
            'null'   => [ 'type' => 'null' ],
            default  => [ 'type' => 'string' ],
        };
    }

    // It's a class/interface name: add to $defs and reference it
    $short = new ReflectionClass($name)->getShortName();
    if (!isset($defs[$short])) {
        $defs[$short] = [
            'title' => $short,
            'type'  => 'object',
            'additionalProperties' => true,
        ];
    }
    return [ '$ref' => '#/$defs/' . $short ];
}

/**
 * Build JSON Schema for a given class name.
 * @throws ReflectionException
 */
function buildSchemaFor(string $fqcn): array
{
    if (!class_exists($fqcn)) {
        fwrite(STDERR, "Class not found: {$fqcn}\n");
        exit(1);
    }

    $rc = new ReflectionClass($fqcn);
    $shortName = $rc->getShortName();

    $defs = [];
    $propertiesSchema = [];

    // public properties declared in class and parents/traits
    foreach ($rc->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
        $type = $property->getType();
        if ($type === null) {
            // Fallback: allow anything
            $propertiesSchema[$property->getName()] = [ 'nullable' => true ];
            continue;
        }

        if ($type instanceof ReflectionUnionType) {
            $unionTypes = $type->getTypes();
            $hasArray = false;
            $hasNull = false;
            $nonArrayOptions = [];

            foreach ($unionTypes as $named) {
                $tname = $named->getName();
                if ($tname === 'null') { $hasNull = true; continue; }
                if ($tname === 'array') { $hasArray = true; continue; }
                $nonArrayOptions[] = mapTypeToJsonSchema($named, $defs);
            }

            $oneOf = [];
            if ($hasNull) { $oneOf[] = [ 'type' => 'null' ]; }
            // direct non-array options (e.g., string, ImageObject, etc.)
            foreach ($nonArrayOptions as $opt) { $oneOf[] = $opt; }
            // if 'array' is part of the union, emit an array of the non-array item types
            if ($hasArray && count($nonArrayOptions) > 0) {
                $oneOf[] = [ 'type' => 'array', 'items' => [ 'oneOf' => $nonArrayOptions ] ];
            } elseif ($hasArray) {
                // fallback: array without item constraints
                $oneOf[] = [ 'type' => 'array' ];
            }

            $propertiesSchema[$property->getName()] = [ 'oneOf' => $oneOf ];
            continue;
        }

        if ($type instanceof ReflectionNamedType) {
            $schema = mapTypeToJsonSchema($type, $defs);
            if ($type->allowsNull() && !($schema['type'] ?? null) === 'null') {
                // normalize to oneOf with null
                $propertiesSchema[$property->getName()] = [
                    'oneOf' => [ [ 'type' => 'null' ], $schema ],
                ];
            } else {
                $propertiesSchema[$property->getName()] = $schema;
            }
            continue;
        }
    }

    ksort($propertiesSchema);
    ksort($defs);

    return [
        '$schema'              => 'https://json-schema.org/draft/2020-12/schema',
        '$id'                  => 'https://schema.oihana.xyz/' . $shortName . '.json',
        'title'                => $shortName,
        'type'                 => 'object',
        'additionalProperties' => false,
        'properties'           => $propertiesSchema,
        '$defs'                => $defs,
    ];
}

// Entry point (only when executed directly)
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $fqcn = $argv[1] ?? null;
    if (!$fqcn) {
        fwrite(STDERR, "Usage: php bin/generate-schema.php Fully\\Qualified\\ClassName\n");
        exit(1);
    }

    $schema = buildSchemaFor($fqcn);

    $rc = new ReflectionClass($fqcn);
    $ns = $rc->getNamespaceName();
    $nsPath = str_replace('\\', '/', $ns);
    $baseDir = __DIR__ . '/../schemas/' . $nsPath;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }
    $outFile = $baseDir . '/' . $rc->getShortName() . '.schema.json';

    file_put_contents($outFile, json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

    echo "Written: {$outFile}\n";
}


