<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/generate-schema.php';

function classFromFile(string $file, string $baseDir, string $baseNs): ?string
{
    if (!str_ends_with($file, '.php'))
    {
        return null;
    }

    $rel = substr($file, strlen($baseDir));
    $rel = ltrim($rel, DIRECTORY_SEPARATOR);

    return $baseNs . str_replace(['/', '\\'], '\\', substr($rel, 0, -4));
}

$roots = [
    [ 'src' => realpath(__DIR__ . '/../src/org/schema'), 'ns' => 'org\\schema\\' ],
    [ 'src' => realpath(__DIR__ . '/../src/xyz/oihana/schema'), 'ns' => 'xyz\\oihana\\schema\\' ],
];
$schemasRoot = __DIR__ . '/../schemas';

if ($srcDir === false) {
    fwrite(STDERR, "Source directory not found\n");
    exit(1);
}

// Cleanup: remove previously generated *.schema.json files to avoid stale artifacts
if (!is_dir($schemasRoot)) { mkdir($schemasRoot, 0777, true); }
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($schemasRoot)) as $path) {
    if ($path->isFile() && str_ends_with($path->getFilename(), '.schema.json')) {
        @unlink($path->getPathname());
    }
}

$ok = 0; $fail = 0; $total = 0;
foreach ($roots as $root) {
    $srcDir = $root['src'];
    $baseNs = $root['ns'];
    if ($srcDir === false) { continue; }

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir));
    // Skip list (empty by default)
    $skipFiles = [];

    foreach ($rii as $file) {
        if ($file->isDir()) { continue; }
        if (in_array($file->getPathname(), $skipFiles, true)) { continue; }
        $fqcn = classFromFile($file->getPathname(), $srcDir . DIRECTORY_SEPARATOR, $baseNs);
        if (!$fqcn) { continue; }
        if (str_contains($fqcn, '\\traits\\')) { continue; }
        if (!class_exists($fqcn)) { continue; }

        try {
            $schema = buildSchemaFor($fqcn);
            $rc = new ReflectionClass($fqcn);
            $ns = $rc->getNamespaceName();
            $nsPath = str_replace('\\', '/', $ns);
            $baseOut = $schemasRoot . '/' . $nsPath;
            if (!is_dir($baseOut)) { mkdir($baseOut, 0777, true); }
            $outFile = $baseOut . '/' . $rc->getShortName() . '.schema.json';
            file_put_contents($outFile, json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
            fwrite(STDOUT, "Written: {$outFile}\n");
            $ok++;
        } catch (Throwable $e) {
            fwrite(STDERR, "Error generating for {$fqcn}: " . $e->getMessage() . "\n");
            $fail++;
        }
        $total++;
    }
}

fwrite(STDOUT, "Done. Success: {$ok}, Failed: {$fail}\n");


