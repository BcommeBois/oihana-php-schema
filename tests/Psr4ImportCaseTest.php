<?php

namespace tests ;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Guards against `use` statements whose case does not match the file on disk.
 *
 * PHP resolves class names case-insensitively, but PSR-4 autoloading turns a
 * fully-qualified name straight into a file path — so `use org\schema\creativeWork\Website;`
 * makes Composer look for `Website.php` when the file is `WebSite.php`. On a
 * case-insensitive filesystem (macOS by default) the class still loads and
 * nothing looks wrong ; on the Linux CI and in production it does not exist.
 *
 * The check therefore has to compare against the real directory listing rather
 * than call `file_exists()`, which is itself case-insensitive on macOS and would
 * happily report the wrong file as present.
 */
class Psr4ImportCaseTest extends TestCase
{
    /**
     * The project's own PSR-4 roots, as declared in composer.json. Imports of
     * anything else (vendor code, PHP built-ins) are out of scope here.
     */
    private const array PSR4 =
    [
        'com\\progress\\' => 'src/com/progress' ,
        'org\\schema\\'   => 'src/org/schema'   ,
        'xyz\\oihana\\'   => 'src/xyz/oihana'   ,
        'tests\\'         => 'tests'            ,
    ];

    public function testEveryImportMatchesItsFileCase(): void
    {
        $root       = dirname( __DIR__ ) ;
        $mismatches = [] ;

        foreach ( [ 'src' , 'tests' ] as $directory )
        {
            foreach ( $this->phpFiles( $root . '/' . $directory ) as $file )
            {
                foreach ( $this->imports( $file ) as $fqcn )
                {
                    $path = $this->psr4Path( $root , $fqcn ) ;

                    if ( $path === null || $this->existsWithExactCase( $path ) )
                    {
                        continue ;
                    }

                    $mismatches[] = sprintf
                    (
                        '%s imports %s, which PSR-4 resolves to %s — no such file (case-sensitive).' ,
                        str_replace( $root . '/' , '' , $file ) ,
                        $fqcn ,
                        str_replace( $root . '/' , '' , $path )
                    );
                }
            }
        }

        $this->assertSame( [] , $mismatches , implode( PHP_EOL , $mismatches ) ) ;
    }

    /**
     * @return string[] Absolute paths of every PHP file under the given directory.
     */
    private function phpFiles( string $directory ): array
    {
        $files    = [] ;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ) ) ;

        /** @var SplFileInfo $file */
        foreach ( $iterator as $file )
        {
            if ( $file->isFile() && $file->getExtension() === 'php' )
            {
                $files[] = $file->getPathname() ;
            }
        }

        return $files ;
    }

    /**
     * @return string[] The class imports of a file — `use function` and `use const` excluded.
     */
    private function imports( string $file ): array
    {
        $source  = file_get_contents( $file ) ;
        $pattern = '/^use\s+(?!function\s|const\s)([A-Za-z0-9_\\\\]+)\s*(?:as\s+\w+\s*)?;/m' ;

        return preg_match_all( $pattern , $source , $matches ) ? $matches[ 1 ] : [] ;
    }

    /**
     * The file a project import resolves to, or `null` when the name belongs to
     * no PSR-4 root of this project.
     */
    private function psr4Path( string $root , string $fqcn ): ?string
    {
        foreach ( self::PSR4 as $prefix => $directory )
        {
            if ( str_starts_with( $fqcn , $prefix ) )
            {
                $relative = str_replace( '\\' , '/' , substr( $fqcn , strlen( $prefix ) ) ) ;

                return $root . '/' . $directory . '/' . $relative . '.php' ;
            }
        }

        return null ;
    }

    /**
     * `file_exists()` is case-insensitive on macOS, so the directory listing is
     * the only trustworthy answer.
     */
    private function existsWithExactCase( string $path ): bool
    {
        $directory = dirname( $path ) ;

        return is_dir( $directory ) && in_array( basename( $path ) , scandir( $directory ) , true ) ;
    }
}
