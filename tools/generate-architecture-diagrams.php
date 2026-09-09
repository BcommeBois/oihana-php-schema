<?php

declare(strict_types=1);

/**
 * Draws the three architecture diagrams of the library as standalone SVG files.
 *
 *   assets/images/architecture-layers-{fr,en}.svg     the layers : the Oihana domains and base, the
 *                                                     Schema.org vocabulary and core, the foundations
 *   assets/images/architecture-anchors-{fr,en}.svg    the Schema.org types the Oihana layer extends,
 *                                                     with the bridge class when there is one
 *   assets/images/architecture-coupling-{fr,en}.svg   the dependencies between the Oihana domains
 *
 * Everything is measured in src/ at run time : the class counts per namespace, the `extends`
 * clauses, the `use` statements between sub-namespaces. The script declares only what cannot be
 * measured — the layout (which section an anchor is drawn in, where a domain sits on the coupling
 * graph) and a handful of descriptive labels. A new class, a new anchor or a new domain therefore
 * shows up on the next run, in a fallback position when its layout is not declared yet, with a
 * notice on stderr saying what to declare.
 *
 * The output is deterministic : running the script twice on the same sources writes the same
 * bytes, so a diff on the SVG files means the code moved.
 *
 * Usage:
 *   php tools/generate-architecture-diagrams.php [--out=assets/images] [--lang=fr,en]
 *   composer schema:diagrams
 *
 * The figures are embedded by wiki/fr/architecture.md and wiki/en/architecture.md.
 */

// ---------------------------------------------------------------------------
// Declared knowledge — the layout and the labels. Everything else is measured.
// ---------------------------------------------------------------------------

const WIDTH  = 1080 ;   // width of the drawing of every figure, in user units
const MARGIN = 32 ;     // inner margin between the canvas edge and the drawing

/** The two source roots : directory, namespace, and the file + constant carrying the JSON-LD context. */
const ROOTS =
[
    'org' => [ 'dir' => 'src/org/schema'       , 'ns' => 'org\\schema'         , 'context' => [ 'Thing.php'            , 'CONTEXT' ] ] ,
    'xyz' => [ 'dir' => 'src/xyz/oihana/schema', 'ns' => 'xyz\\oihana\\schema' , 'context' => [ 'constants/Oihana.php' , 'SCHEMA'  ] ] ,
] ;

/** Sub-directories of xyz\oihana\schema that form the shared base rather than a domain. */
const XYZ_BASE_DIRS = [ 'constants' , 'enumerations' , 'helpers' , 'traits' ] ;

/** Sub-directories of org\schema that form the core rather than the vocabulary. */
const ORG_CORE_DIRS = [ 'constants' , 'helpers' , 'traits' ] ;

/** Nested sub-namespaces drawn as domains of their own ; the parent keeps the rest of its classes. */
const XYZ_SPLIT_DOMAINS = [ 'business/documents' ] ;

/** Figure 2 — the section each Schema.org anchor is drawn in. An unlisted anchor falls into 'others'. */
const ANCHOR_SECTIONS =
[
    'actors'   => [ 'Person' , 'Corporation' , 'Place' , 'PostalAddress' ] ,
    'values'   => [ 'Intangible' , 'StructuredValue' , 'Observation' ] ,
    'products' => [ 'SomeProducts' , 'OfferForPurchase' , 'UnitPriceSpecification' , 'QuantitativeValue' , 'PaymentMethod' ] ,
    'skos'     => [ 'DefinedTerm' , 'DefinedTermSet' ] ,
    'actions'  => [ 'Thing' , 'Event' , 'ScheduleAction' , 'CreativeWork' , 'Action' , 'InviteAction' , 'UpdateAction' , 'WebAPI' , 'SoftwareApplication' ] ,
    'enums'    => [ 'Enumeration' , 'StatusEnumeration' , 'BusinessEntityType' , 'PriceTypeEnumeration' , 'PriceComponentTypeEnumeration' ] ,
] ;

/** Figure 3 — the [ column , row ] of each domain. An unlisted domain is appended below, with a notice. */
const COUPLING_GRID =
[
    'business/documents' => [ 0 , 0 ] , 'statistics' => [ 1 , 0 ] , 'thesaurus' => [ 2 , 0 ] , 'shipping'     => [ 3 , 0 ] ,
    'organizations'      => [ 0 , 1 ] , 'products'   => [ 1 , 1 ] , 'places'    => [ 2 , 1 ] , 'people'       => [ 3 , 1 ] ,
                                        'business'   => [ 1 , 2 ] , 'auth'      => [ 2 , 2 ] , 'appointments' => [ 3 , 2 ] ,
] ;
const COUPLING_ISOLATED_CELL = [ 0 , 2 ] ;   // where the domains without any link are listed

/** Examples quoted in the descriptive labels ; an example that no longer exists is dropped silently. */
const ORG_ROOT_EXAMPLES     = [ 'Thing' , 'Intangible' , 'StructuredValue' , 'Enumeration' , 'Person' , 'Organization' , 'Place' , 'Event' , 'Product' , 'Offer' ] ;
const ORG_CONSTANT_EXAMPLES = [ 'JsonLD' , 'ArangoDB' , 'Edge' , 'I18n' ] ;
const ORG_TRAIT_EXAMPLES    = [ 'ValueTrait' , 'PlaceTrait' , 'CollectionTrait' ] ;
const XYZ_REGISTRY_EXAMPLES = [ 'JwtClaim' , 'CasbinPolicy' , 'ApplicationType' ] ;

/** What each foundation package brings ; an unlisted package gets no description. */
const PACKAGE_NOTES =
[
    'oihana/php-reflect' => 'Reflection::hydrate() · JsonSchemaTrait · SerializationContext' ,
    'oihana/php-core'    => 'options, accessors' ,
] ;

const LABELS =
[
    'fr' =>
    [
        'class'           => 'classe' ,
        'classes'         => 'classes' ,
        'root'            => 'racine' ,
        'incl'            => 'dont' ,
        'domains'         => '— les domaines métier · %d classes' ,
        'base'            => '— le socle maison, partagé par tous les domaines' ,
        'vocabulary'      => '— le vocabulaire Schema.org · %d classes' ,
        'core'            => '— le noyau, hérité par toute entité' ,
        'foundations'     => 'fondations' ,
        'foundationsRole' => '— PHP et les paquets oihana' ,
        'seam'            => 'étend — toujours dans ce sens' ,
        'seamNone'        => "aucun fichier de org\\schema n'importe xyz\\oihana\\schema" ,
        'seamSome'        => "%d fichier(s) de org\\schema importent xyz\\oihana\\schema — à corriger" ,
        'reflect'         => "s'appuie sur php-reflect pour hydrater (Reflection::hydrate) et sérialiser (JsonSchemaTrait)" ,
        'baseOihana'      => 'Properties (org) + %d traits Oihana · %d traits de constantes' ,
        'registries'      => '%d registres · %s…' ,
        'mixins'          => '%d mixins' ,
        'functions'       => '%d fonctions · %s' ,
        'coreSchema'      => 'Properties · %d traits de constantes (%s…)' ,
        'coreTraits'      => '%d mixins · %s…' ,
        'coreHelpers'     => '%s + %d hydrateurs' ,
        'coreApart'       => 'à part · %s' ,
        'aria1'           => 'Cinq bandes empilées : les domaines Oihana, le socle Oihana, le vocabulaire Schema.org, le noyau Schema.org, puis PHP et les paquets oihana. Les flèches descendent toujours.' ,
        'col1'            => 'org\\schema — le type étendu' ,
        'col2'            => 'xyz — la classe-pont' ,
        'col3'            => 'xyz — les feuilles' ,
        'extends'         => 'flèche = extends' ,
        'sections'        =>
        [
            'actors'   => 'Acteurs et lieux' ,
            'values'   => 'Documents, statistiques et valeurs' ,
            'products' => 'Produits et tarifs' ,
            'skos'     => 'Vocabulaire contrôlé (SKOS)' ,
            'actions'  => 'Rendez-vous, actions et web' ,
            'enums'    => 'Énumérations' ,
            'others'   => 'Autres ancrages — section à déclarer dans ANCHOR_SECTIONS' ,
        ] ,
        'noParent'        => 'Hors héritage Schema.org : %s' ,
        'aria2'           => '%d types Schema.org, à gauche, portent toutes les classes Oihana, à droite ; quand une classe-pont existe elle apparaît dans la colonne du milieu.' ,
        'legend3'         => "flèche = utilise le type d'une propriété · épaisseur = nombre de classes référencées · orange ⇄ = référence mutuelle" ,
        'isolated'        => 'aucun lien entre domaines' ,
        'unplaced'        => 'à placer dans COUPLING_GRID' ,
        'mutualTitle'     => 'Les références mutuelles, classe par classe' ,
        'aria3'           => '%d domaines Oihana reliés par les types de leurs propriétés ; %d paires se référencent mutuellement.' ,
    ] ,
    'en' =>
    [
        'class'           => 'class' ,
        'classes'         => 'classes' ,
        'root'            => 'root' ,
        'incl'            => 'incl.' ,
        'domains'         => '— the domain layer · %d classes' ,
        'base'            => '— the shared base, used by every domain' ,
        'vocabulary'      => '— the Schema.org vocabulary · %d classes' ,
        'core'            => '— the core, inherited by every entity' ,
        'foundations'     => 'foundations' ,
        'foundationsRole' => '— PHP and the oihana packages' ,
        'seam'            => 'extends — always in this direction' ,
        'seamNone'        => 'no file of org\\schema imports xyz\\oihana\\schema' ,
        'seamSome'        => '%d file(s) of org\\schema import xyz\\oihana\\schema — to be fixed' ,
        'reflect'         => 'relies on php-reflect to hydrate (Reflection::hydrate) and serialize (JsonSchemaTrait)' ,
        'baseOihana'      => 'Properties (org) + %d Oihana traits · %d constant traits' ,
        'registries'      => '%d registries · %s…' ,
        'mixins'          => '%d mixins' ,
        'functions'       => '%d functions · %s' ,
        'coreSchema'      => 'Properties · %d constant traits (%s…)' ,
        'coreTraits'      => '%d mixins · %s…' ,
        'coreHelpers'     => '%s + %d hydrators' ,
        'coreApart'       => 'apart · %s' ,
        'aria1'           => 'Five stacked bands: the Oihana domains, the Oihana base, the Schema.org vocabulary, the Schema.org core, then PHP and the oihana packages. The arrows always point down.' ,
        'col1'            => 'org\\schema — the type extended' ,
        'col2'            => 'xyz — the bridge class' ,
        'col3'            => 'xyz — the leaves' ,
        'extends'         => 'arrow = extends' ,
        'sections'        =>
        [
            'actors'   => 'Actors and places' ,
            'values'   => 'Documents, statistics and values' ,
            'products' => 'Products and prices' ,
            'skos'     => 'Controlled vocabulary (SKOS)' ,
            'actions'  => 'Appointments, actions and web' ,
            'enums'    => 'Enumerations' ,
            'others'   => 'Other anchors — section to declare in ANCHOR_SECTIONS' ,
        ] ,
        'noParent'        => 'Outside the Schema.org inheritance: %s' ,
        'aria2'           => '%d Schema.org types, on the left, carry every Oihana class, on the right; when a bridge class exists it appears in the middle column.' ,
        'legend3'         => 'arrow = uses the type of a property · thickness = number of referenced classes · orange ⇄ = mutual reference' ,
        'isolated'        => 'no link between domains' ,
        'unplaced'        => 'to place in COUPLING_GRID' ,
        'mutualTitle'     => 'The mutual references, class by class' ,
        'aria3'           => '%d Oihana domains linked by the types of their properties; %d pairs reference each other.' ,
    ] ,
] ;

// ---------------------------------------------------------------------------
// Measuring src/
// ---------------------------------------------------------------------------

final class ClassInfo
{
    /**
     * @param list<string>          $implements Short names of the implemented interfaces.
     * @param list<string>          $traits     Short names of the traits used in the body.
     * @param array<string, string> $imports    File-level imports : alias => fully qualified name.
     */
    public function __construct
    (
        public readonly string  $root       ,
        public readonly string  $dir        ,   // directory relative to the root, '' at the root
        public readonly string  $kind       ,   // class | interface | trait | enum
        public readonly string  $name       ,
        public readonly string  $fqcn       ,
        public readonly ?string $parent     ,   // resolved fully qualified name, null when none
        public readonly array   $implements ,
        public readonly array   $traits     ,
        public readonly array   $imports    ,
    ) {}

    public function isClass() : bool
    {
        return $this->kind === 'class' || $this->kind === 'interface' ;
    }

    /** The first segment of the directory ('' at the root). */
    public function topDir() : string
    {
        return explode( '/' , $this->dir )[0] ;
    }
}

final class Sources
{
    /** @var array<string, ClassInfo> fully qualified name => info */
    public array $classes = [] ;

    /** @var array<string, array<string, int>> root => directory => number of free functions */
    public array $functions = [] ;

    /** @var list<string> */
    public array $notices = [] ;

    public function __construct( public readonly string $projectDir ) {}

    public function scan() : void
    {
        foreach ( ROOTS as $root => $spec )
        {
            $base  = $this->projectDir . '/' . $spec['dir'] ;
            $files = [] ;

            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base , FilesystemIterator::SKIP_DOTS ) ) ;

            foreach ( $iterator as $file )
            {
                if ( $file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php' )
                {
                    $files[] = $file->getPathname() ;
                }
            }

            sort( $files ) ;

            foreach ( $files as $path )
            {
                $rel = substr( $path , strlen( $base ) + 1 ) ;
                $dir = dirname( $rel ) ;
                $this->parse( $path , $root , $dir === '.' ? '' : $dir ) ;
            }
        }
    }

    private function parse( string $path , string $root , string $dir ) : void
    {
        $code = (string) file_get_contents( $path ) ;

        if ( preg_match_all( '/^function\s+(\w+)\s*\(/m' , $code , $functions ) > 0 )
        {
            $this->functions[ $root ][ $dir ] = ( $this->functions[ $root ][ $dir ] ?? 0 ) + count( $functions[1] ) ;
        }

        $namespace = preg_match( '/^namespace\s+([\w\\\\]+)\s*;/m' , $code , $m ) === 1 ? $m[1] : '' ;

        $imports = [] ;
        preg_match_all( '/^use\s+(?!function\s|const\s)([\w\\\\]+)(?:\s+as\s+(\w+))?\s*;/m' , $code , $uses , PREG_SET_ORDER ) ;
        foreach ( $uses as $use )
        {
            $alias = ( $use[2] ?? '' ) !== '' ? $use[2] : self::shortName( $use[1] ) ;
            $imports[ $alias ] = $use[1] ;
        }

        $declaration = '/^(?:(?:abstract|final|readonly)\s+)*(class|interface|trait|enum)\s+(\w+)(?:\s*:\s*\w+)?(?:\s+extends\s+([\w\\\\]+))?(?:\s+implements\s+([\w\\\\\s,]+?))?\s*(?:\{|$)/m' ;

        if ( preg_match( $declaration , $code , $d , PREG_UNMATCHED_AS_NULL ) !== 1 )
        {
            return ;
        }

        $kind   = (string) $d[1] ;
        $name   = (string) $d[2] ;
        $parent = isset( $d[3] ) ? self::resolve( $d[3] , $namespace , $imports ) : null ;

        $implements = [] ;
        foreach ( explode( ',' , (string) ( $d[4] ?? '' ) ) as $interface )
        {
            $interface = trim( $interface ) ;
            if ( $interface !== '' )
            {
                $implements[] = self::shortName( $interface ) ;
            }
        }

        // The traits used in the body : the indented `use A , B ;` statements that open the class.
        $body   = substr( $code , (int) strpos( $code , (string) $d[0] ) + strlen( (string) $d[0] ) ) ;
        $traits = [] ;
        if ( preg_match_all( '/^[ \t]+use\s+([^;{]+)[;{]/m' , $body , $traitUses ) > 0 )
        {
            foreach ( $traitUses[1] as $list )
            {
                $list = (string) preg_replace( '~//[^\n]*~' , '' , $list ) ;
                foreach ( explode( ',' , $list ) as $trait )
                {
                    $trait = trim( $trait ) ;
                    if ( $trait !== '' )
                    {
                        $traits[] = self::shortName( $trait ) ;
                    }
                }
            }
        }

        $fqcn = ( $namespace !== '' ? $namespace . '\\' : '' ) . $name ;

        $this->classes[ $fqcn ] = new ClassInfo( $root , $dir , $kind , $name , $fqcn , $parent , $implements , $traits , $imports ) ;
    }

    /** @param array<string, string> $imports */
    private static function resolve( string $name , string $namespace , array $imports ) : string
    {
        if ( str_starts_with( $name , '\\' ) )
        {
            return substr( $name , 1 ) ;
        }

        $first = explode( '\\' , $name )[0] ;

        if ( isset( $imports[ $first ] ) )
        {
            return $imports[ $first ] . substr( $name , strlen( $first ) ) ;
        }

        return $namespace !== '' ? $namespace . '\\' . $name : $name ;
    }

    public static function shortName( string $fqcn ) : string
    {
        $pos = strrpos( $fqcn , '\\' ) ;
        return $pos === false ? $fqcn : substr( $fqcn , $pos + 1 ) ;
    }

    /**
     * The declarations of a root whose directory is $dir (or below it when $recursive).
     * @return list<ClassInfo>
     */
    public function in( string $root , string $dir = '' , bool $recursive = true , ?string $kind = 'class' ) : array
    {
        $out = [] ;
        foreach ( $this->classes as $info )
        {
            if ( $info->root !== $root )
            {
                continue ;
            }
            if ( $kind === 'class' ? !$info->isClass() : ( $kind !== null && $info->kind !== $kind ) )
            {
                continue ;
            }
            $inside = $recursive
                ? ( $dir === '' || $info->dir === $dir || str_starts_with( $info->dir , $dir . '/' ) )
                : $info->dir === $dir ;
            if ( $inside )
            {
                $out[] = $info ;
            }
        }
        usort( $out , static fn( ClassInfo $a , ClassInfo $b ) => strcmp( $a->fqcn , $b->fqcn ) ) ;
        return $out ;
    }

    public function count( string $root , string $dir = '' , bool $recursive = true , ?string $kind = 'class' ) : int
    {
        return count( $this->in( $root , $dir , $recursive , $kind ) ) ;
    }

    public function functionsIn( string $root , string $dir ) : int
    {
        $n = 0 ;
        foreach ( $this->functions[ $root ] ?? [] as $d => $count )
        {
            if ( $d === $dir || str_starts_with( $d , $dir . '/' ) )
            {
                $n += $count ;
            }
        }
        return $n ;
    }

    public function find( string $root , string $shortName ) : ?ClassInfo
    {
        foreach ( $this->classes as $info )
        {
            if ( $info->root === $root && $info->name === $shortName )
            {
                return $info ;
            }
        }
        return null ;
    }

    /** The value of `const string NAME = '…'` in a file of a root, or null. */
    public function constant( string $root , string $relFile , string $name ) : ?string
    {
        $path = $this->projectDir . '/' . ROOTS[ $root ]['dir'] . '/' . $relFile ;
        if ( !is_file( $path ) )
        {
            return null ;
        }
        $code = (string) file_get_contents( $path ) ;
        return preg_match( '/const\s+string\s+' . preg_quote( $name , '/' ) . '\s*=\s*\'([^\']+)\'/' , $code , $m ) === 1 ? $m[1] : null ;
    }

    /** Keeps the examples that still exist as declarations of the root. @param list<string> $examples @return list<string> */
    public function existing( string $root , array $examples ) : array
    {
        $names = [] ;
        foreach ( $this->classes as $info )
        {
            if ( $info->root === $root )
            {
                $names[ $info->name ] = true ;
            }
        }
        return array_values( array_filter( $examples , static fn( string $e ) => isset( $names[ $e ] ) ) ) ;
    }
}

/** The domain key of a directory of xyz\oihana\schema : '' at the root, null for the base directories. */
function domainKey( string $dir ) : ?string
{
    if ( $dir === '' )
    {
        return '' ;
    }
    foreach ( XYZ_SPLIT_DOMAINS as $split )
    {
        if ( $dir === $split || str_starts_with( $dir , $split . '/' ) )
        {
            return $split ;
        }
    }
    $top = explode( '/' , $dir )[0] ;
    foreach ( XYZ_SPLIT_DOMAINS as $split )
    {
        if ( str_starts_with( $split , $top . '/' ) && ( $dir === $top || str_starts_with( $dir , $top . '/' ) ) )
        {
            return $top ;   // the parent of a split keeps its own classes
        }
    }
    return in_array( $top , XYZ_BASE_DIRS , true ) ? null : $top ;
}

/** @return array<string, string> label key => value, for one language */
function labels( string $lang ) : array
{
    /** @var array<string, string> $flat */
    $flat = array_filter( LABELS[ $lang ] , 'is_string' ) ;
    return $flat ;
}

function classesLabel( int $n , string $lang ) : string
{
    return $n . ' ' . ( $n === 1 ? LABELS[ $lang ]['class'] : LABELS[ $lang ]['classes'] ) ;
}

function domainLabel( string $key , string $lang ) : string
{
    return $key === '' ? LABELS[ $lang ]['root'] : str_replace( '/' , '\\' , $key ) ;
}

// ---------------------------------------------------------------------------
// The model — everything the three figures need, measured once
// ---------------------------------------------------------------------------

final class Model
{
    /** @var array<string, int> domain key => number of classes */
    public array $xyzDomains = [] ;

    /** @var array<string, list<string>> domain key => sub-directories holding classes */
    public array $xyzDomainSubdirs = [] ;

    /** @var array<string, int> '' or top directory => number of classes */
    public array $orgVocabulary = [] ;

    /**
     * Figure 2 : anchor fqcn => [ 'name' => …, 'bridges' => [ bridge name => leaves ], 'direct' => leaves ]
     * where a leaf is [ 'name' => …, 'domain' => domain label key ].
     * @var array<string, array{name: string, bridges: array<string, list<array{name: string, domain: string}>>, direct: list<array{name: string, domain: string}>, size: int}>
     */
    public array $anchors = [] ;

    /** @var list<array{name: string, domain: string}> Oihana classes with no Schema.org ancestor */
    public array $noParent = [] ;

    /** @var array<string, array<string, list<string>>> source domain => target domain => "Class→Target" */
    public array $edges = [] ;

    public int $orgImportsXyz = 0 ;

    public function __construct( public readonly Sources $src )
    {
        $this->measureDomains() ;
        $this->measureAnchors() ;
        $this->measureCoupling() ;
    }

    private function measureDomains() : void
    {
        foreach ( $this->src->in( 'xyz' ) as $info )
        {
            $key = domainKey( $info->dir ) ;
            if ( $key === null )
            {
                continue ;
            }
            $this->xyzDomains[ $key ] = ( $this->xyzDomains[ $key ] ?? 0 ) + 1 ;
            if ( $key !== '' && $info->dir !== $key )
            {
                $sub = substr( $info->dir , strlen( $key ) + 1 ) ;
                $this->xyzDomainSubdirs[ $key ] ??= [] ;
                if ( !in_array( $sub , $this->xyzDomainSubdirs[ $key ] , true ) )
                {
                    $this->xyzDomainSubdirs[ $key ][] = $sub ;
                }
            }
        }
        foreach ( $this->src->in( 'org' ) as $info )
        {
            $top = $info->topDir() ;
            if ( in_array( $top , ORG_CORE_DIRS , true ) )
            {
                continue ;
            }
            $this->orgVocabulary[ $top ] = ( $this->orgVocabulary[ $top ] ?? 0 ) + 1 ;
        }
        // Heaviest first, then by name : the figure says where the mass is.
        $byMass = static function( array &$counts ) : void
        {
            uksort( $counts , static fn( string $a , string $b ) => [ $counts[ $b ] , $a ] <=> [ $counts[ $a ] , $b ] ) ;
        } ;
        $byMass( $this->xyzDomains ) ;
        $byMass( $this->orgVocabulary ) ;
    }

    private function measureAnchors() : void
    {
        $children = [] ;
        $candidates = [] ;
        foreach ( $this->src->in( 'xyz' ) as $info )
        {
            if ( in_array( $info->topDir() , [ 'constants' , 'helpers' , 'traits' ] , true ) )
            {
                continue ;
            }
            $candidates[] = $info ;
            if ( $info->parent !== null )
            {
                $children[ $info->parent ][] = $info->fqcn ;
            }
        }

        foreach ( $candidates as $info )
        {
            $domain = domainKey( $info->dir ) ?? $info->topDir() ;
            $leaf   = [ 'name' => $info->name , 'domain' => $domain ] ;

            // Climb the Oihana ancestors up to the first Schema.org type.
            $chain  = [ $info ] ;
            $parent = $info->parent ;
            while ( $parent !== null && isset( $this->src->classes[ $parent ] ) && $this->src->classes[ $parent ]->root === 'xyz' )
            {
                $chain[] = $this->src->classes[ $parent ] ;
                $parent  = $this->src->classes[ $parent ]->parent ;
            }

            $anchor = $parent !== null ? ( $this->src->classes[ $parent ] ?? null ) : null ;
            if ( $anchor === null || $anchor->root !== 'org' )
            {
                if ( $parent !== null )
                {
                    $this->src->notices[] = "{$info->fqcn} extends {$parent}, which is not a Schema.org type : left out of the anchors figure." ;
                }
                $this->noParent[] = $leaf ;
                continue ;
            }

            $this->anchors[ $anchor->fqcn ] ??= [ 'name' => $anchor->name , 'bridges' => [] , 'direct' => [] , 'size' => 0 ] ;
            $this->anchors[ $anchor->fqcn ]['size']++ ;

            if ( count( $chain ) === 1 )
            {
                if ( isset( $children[ $info->fqcn ] ) )
                {
                    $this->anchors[ $anchor->fqcn ]['bridges'][ $info->name ] ??= [] ;   // a bridge : listed through its children
                }
                else
                {
                    $this->anchors[ $anchor->fqcn ]['direct'][] = $leaf ;
                }
            }
            else
            {
                $bridge = $chain[ count( $chain ) - 1 ]->name ;
                $this->anchors[ $anchor->fqcn ]['bridges'][ $bridge ][] = $leaf ;
            }
        }

        $byDomainThenName = static fn( array $a , array $b ) => [ $a['domain'] , $a['name'] ] <=> [ $b['domain'] , $b['name'] ] ;
        foreach ( $this->anchors as &$anchor )
        {
            usort( $anchor['direct'] , $byDomainThenName ) ;
            foreach ( $anchor['bridges'] as &$leaves )
            {
                usort( $leaves , $byDomainThenName ) ;
            }
            unset( $leaves ) ;
            uksort( $anchor['bridges'] , static fn( string $a , string $b ) => [ count( $anchor['bridges'][ $b ] ) , $a ] <=> [ count( $anchor['bridges'][ $a ] ) , $b ] ) ;
        }
        unset( $anchor ) ;
        usort( $this->noParent , $byDomainThenName ) ;
    }

    private function measureCoupling() : void
    {
        $prefix = ROOTS['xyz']['ns'] . '\\' ;

        foreach ( $this->src->in( 'xyz' , '' , true , null ) as $info )
        {
            $source = domainKey( $info->dir ) ;
            if ( $source === null )
            {
                continue ;
            }
            foreach ( $info->imports as $fqcn )
            {
                if ( !str_starts_with( $fqcn , $prefix ) )
                {
                    continue ;
                }
                $parts  = explode( '\\' , substr( $fqcn , strlen( $prefix ) ) ) ;
                $name   = array_pop( $parts ) ;
                $target = domainKey( implode( '/' , $parts ) ) ;
                if ( $target === null || $target === $source )
                {
                    continue ;
                }
                $this->edges[ $source ][ $target ][] = $info->name . '→' . $name ;
            }
        }
        ksort( $this->edges ) ;
        foreach ( $this->edges as &$targets )
        {
            ksort( $targets ) ;
            foreach ( $targets as &$refs )
            {
                $refs = array_values( array_unique( $refs ) ) ;
                sort( $refs ) ;
            }
            unset( $refs ) ;
        }
        unset( $targets ) ;

        // The one rule of the architecture, checked rather than assumed.
        $xyz = ROOTS['xyz']['ns'] . '\\' ;
        foreach ( $this->src->in( 'org' , '' , true , null ) as $info )
        {
            foreach ( $info->imports as $fqcn )
            {
                if ( str_starts_with( $fqcn , $xyz ) )
                {
                    $this->orgImportsXyz++ ;
                    $this->src->notices[] = "! {$info->fqcn} imports {$fqcn} : org\\schema must not depend on xyz\\oihana\\schema." ;
                    break ;
                }
            }
        }
    }

    /** @return list<array{a: string, b: string, size: int}> the pairs of domains referencing each other, strongest first */
    public function mutualPairs() : array
    {
        $pairs = [] ;
        foreach ( $this->edges as $a => $targets )
        {
            foreach ( $targets as $b => $refs )
            {
                if ( strcmp( $a , $b ) < 0 && isset( $this->edges[ $b ][ $a ] ) )
                {
                    $pairs[] = [ 'a' => $a , 'b' => $b , 'size' => max( count( $refs ) , count( $this->edges[ $b ][ $a ] ) ) ] ;
                }
            }
        }
        usort( $pairs , static fn( array $p , array $q ) => [ $q['size'] , $p['a'] , $p['b'] ] <=> [ $p['size'] , $q['a'] , $q['b'] ] ) ;
        return $pairs ;
    }
}

// ---------------------------------------------------------------------------
// SVG primitives
// ---------------------------------------------------------------------------

function esc( string $s ) : string
{
    return htmlspecialchars( $s , ENT_QUOTES | ENT_XML1 , 'UTF-8' ) ;
}

/** Estimated width of a string set in the mono stack (≈ 0.62 em per character, the widest fallback). */
function monoW( string $s , float $size = 12.5 ) : float
{
    return mb_strlen( $s ) * $size * 0.62 ;
}

/** Estimated width of a string set in the sans stack (≈ 0.55 em per character). */
function sansW( string $s , float $size = 11.0 ) : float
{
    return mb_strlen( $s ) * $size * 0.55 ;
}

function fmt( float $v ) : string
{
    return rtrim( rtrim( number_format( $v , 1 , '.' , '' ) , '0' ) , '.' ) ;
}

function svgText( float $x , float $y , string $s , string $class , ?string $anchor = null ) : string
{
    $a = $anchor !== null ? ' text-anchor="' . $anchor . '"' : '' ;
    return '<text class="' . $class . '" x="' . fmt( $x ) . '" y="' . fmt( $y ) . '"' . $a . '>' . esc( $s ) . '</text>' ;
}

function svgLine( float $x1 , float $y1 , float $x2 , float $y2 , string $class , string $marker = '' , bool $both = false , string $style = '' ) : string
{
    $m = $marker !== '' ? ' marker-end="url(#' . $marker . ')"' . ( $both ? ' marker-start="url(#' . $marker . ')"' : '' ) : '' ;
    $s = $style !== '' ? ' style="' . $style . '"' : '' ;
    return '<line class="' . $class . '"' . $s . ' x1="' . fmt( $x1 ) . '" y1="' . fmt( $y1 ) . '" x2="' . fmt( $x2 ) . '" y2="' . fmt( $y2 ) . '"' . $m . '/>' ;
}

function svgMarker( string $id , string $class ) : string
{
    return '<marker id="' . $id . '" class="' . $class . '" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="9" markerHeight="9" markerUnits="userSpaceOnUse" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z"/></marker>' ;
}

function svgChip( float $x , float $y , float $w , float $h , string $label , string $hue ) : string
{
    return '<rect class="box ' . $hue . '" x="' . fmt( $x ) . '" y="' . fmt( $y ) . '" width="' . fmt( $w ) . '" height="' . fmt( $h ) . '" rx="3"/>'
         . svgText( $x + 11 , $y + $h / 2 + 4 , $label , 'mono chiptext ' . $hue . '-ink' ) ;
}

const SVG_STYLE = <<<'CSS'
text{fill:#2E302A;font-variant-numeric:tabular-nums}
.mono{font-family:"IBM Plex Mono",ui-monospace,"SF Mono",Menlo,Consolas,"DejaVu Sans Mono",monospace;white-space:pre}
.sans,.tag{font-family:"IBM Plex Sans",-apple-system,"Segoe UI",Helvetica,Arial,sans-serif;white-space:pre}
.ns{font-size:13px;font-weight:500}.role{font-size:12px;fill:#6B6E64}.ctx{font-size:11px;fill:#6B6E64}
.name{font-size:12.5px;font-weight:500}.desc{font-size:11px}.label{font-size:12px}.muted{fill:#6B6E64}
.band{fill:#EBEDE7}.box.org{fill:#DCEAEB}.box.xyz{fill:#F6E3D5}.box.neutral{fill:#E3E5DF}
.org-ink{fill:#0B454B}.xyz-ink{fill:#853B14}.neutral-ink{fill:#4B4D43}
.org-text{fill:#0F5E66}.xyz-text{fill:#C5622B}.neutral-text{fill:#4B4D43}
.edge{stroke:#2E302A;stroke-width:1.2;fill:none}.edge.direct{stroke:#6B6E64}.edge.mutual{stroke:#C5622B}
.mk path{fill:#2E302A}.mk.muted path{fill:#6B6E64}.mk.xyz path{fill:#C5622B}
.rule{stroke:#D8DAD2;stroke-width:1}.colhead{font-size:11.5px;fill:#6B6E64;font-weight:500}
.eyebrow{font-size:10.5px;fill:#6B6E64;letter-spacing:.08em;font-weight:500}
.chiptext{font-size:11.5px;font-weight:500}.leaves{font-size:11.5px}.leaf{fill:#853B14}.tag{font-size:11px;fill:#6B6E64}
.node{fill:#F6E3D5}.node.iso{fill:none;stroke:#D8DAD2;stroke-width:1.2;stroke-dasharray:4 3}
.badge{fill:#C5622B}.badge-text{fill:#F4F5F1;font-size:11px;font-weight:600}
.foot{font-size:10.5px}.foot-title{font-size:11.5px;font-weight:500}
CSS ;

/** @param list<string> $parts */
function svgDocument( array $parts , float $height , string $ariaLabel , string $defs ) : string
{
    $w = WIDTH + 2 * MARGIN ;
    $h = (int) ceil( $height ) + 2 * MARGIN ;
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '" role="img" aria-label="' . esc( $ariaLabel ) . '" xml:space="preserve">' . "\n"
         . '<style>' . "\n" . SVG_STYLE . "\n" . '</style>' . "\n"
         . '<rect width="' . $w . '" height="' . $h . '" fill="#F4F5F1"/>' . "\n"
         . '<defs>' . $defs . '</defs>' . "\n"
         . '<g transform="translate(' . MARGIN . ',' . MARGIN . ')">' . "\n"
         . implode( "\n" , $parts ) . "\n</g>\n</svg>\n" ;
}

// ---------------------------------------------------------------------------
// Figure 1 — the layers
// ---------------------------------------------------------------------------

/**
 * A band of boxes : the header names the namespace and its role, the boxes flow left to right.
 * @param  list<array{0: string, 1: string}> $items [ name , description ]
 * @return array{0: string, 1: float} the svg and the height
 */
function band( float $y , string $ns , string $role , string $context , array $items , string $hue ) : array
{
    $x = 0 ; $w = WIDTH ; $pad = 14 ; $header = 32 ; $boxH = 50 ; $gap = 10 ;

    $rows = [] ; $row = [] ; $cursor = 0.0 ;
    foreach ( $items as $item )
    {
        $bw = max( monoW( $item[0] ) , sansW( $item[1] ) ) + 28 ;
        if ( $row !== [] && $cursor + $bw > $w - 2 * $pad )
        {
            $rows[] = $row ; $row = [] ; $cursor = 0.0 ;
        }
        $row[]   = [ $item , $bw ] ;
        $cursor += $bw + $gap ;
    }
    if ( $row !== [] )
    {
        $rows[] = $row ;
    }

    $h = $header + $pad + count( $rows ) * $boxH + ( count( $rows ) - 1 ) * $gap + $pad ;

    $o   = [] ;
    $o[] = '<rect class="band" x="' . $x . '" y="' . fmt( $y ) . '" width="' . $w . '" height="' . fmt( $h ) . '" rx="8"/>' ;
    $o[] = svgText( $x + $pad , $y + 21 , $ns , 'mono ns ' . $hue . '-text' ) ;
    $o[] = svgText( $x + $pad + monoW( $ns , 13 ) + 10 , $y + 21 , $role , 'sans role' ) ;
    if ( $context !== '' )
    {
        $o[] = svgText( $x + $w - $pad , $y + 21 , $context , 'mono ctx' , 'end' ) ;
    }

    $yy = $y + $header + $pad ;
    foreach ( $rows as $row )
    {
        $xx = $x + $pad ;
        foreach ( $row as [ $item , $bw ] )
        {
            [ $name , $desc ] = $item ;
            $o[] = '<rect class="box ' . $hue . '" x="' . fmt( $xx ) . '" y="' . fmt( $yy ) . '" width="' . fmt( $bw ) . '" height="' . $boxH . '" rx="4"/>' ;
            if ( $desc !== '' )
            {
                $o[] = svgText( $xx + 14 , $yy + 21 , $name , 'mono name ' . $hue . '-ink' ) ;
                $o[] = svgText( $xx + 14 , $yy + 38 , $desc , 'sans desc ' . $hue . '-ink' ) ;
            }
            else
            {
                $o[] = svgText( $xx + 14 , $yy + 30 , $name , 'mono name ' . $hue . '-ink' ) ;
            }
            $xx += $bw + $gap ;
        }
        $yy += $boxH + $gap ;
    }
    return [ implode( "\n" , $o ) , $h ] ;
}

function renderLayers( Model $m , string $lang ) : string
{
    $L   = labels( $lang ) ;
    $src = $m->src ;

    // -- xyz : the domains
    $domains = [] ;
    foreach ( $m->xyzDomains as $key => $count )
    {
        $desc = classesLabel( $count , $lang ) ;
        if ( $key === '' )
        {
            $desc .= ' · ' . implode( ', ' , array_map( static fn( ClassInfo $c ) => $c->name , $src->in( 'xyz' , '' , false ) ) ) ;
        }
        elseif ( isset( $m->xyzDomainSubdirs[ $key ] ) )
        {
            $desc .= ' · ' . $L['incl'] . ' ' . implode( ', ' , $m->xyzDomainSubdirs[ $key ] ) ;
        }
        $domains[] = [ domainLabel( $key , $lang ) , $desc ] ;
    }
    $xyzTotal = array_sum( $m->xyzDomains ) ;

    // -- xyz : the base
    $oihana        = $src->find( 'xyz' , 'Oihana' ) ;
    $oihanaTraits  = $oihana !== null ? count( array_filter( $oihana->traits , static fn( string $t ) => $t !== 'Properties' ) ) : 0 ;
    $registries    = array_filter( $src->in( 'xyz' , 'constants' ) , static fn( ClassInfo $c ) => $c->name !== 'Oihana' ) ;
    $helperDirs    = array_keys( array_filter( $src->functions['xyz'] ?? [] , static fn( string $d ) => str_starts_with( $d , 'helpers/' ) , ARRAY_FILTER_USE_KEY ) ) ;
    $helperDirs    = array_values( array_unique( array_map( static fn( string $d ) => explode( '/' , $d )[1] , $helperDirs ) ) ) ;
    sort( $helperDirs ) ;
    $base =
    [
        [ 'constants\\Oihana' , sprintf( $L['baseOihana'] , $oihanaTraits , $src->count( 'xyz' , 'constants' , true , 'trait' ) ) ] ,
        [ 'constants\\*'      , sprintf( $L['registries'] , count( $registries ) , implode( ', ' , $src->existing( 'xyz' , XYZ_REGISTRY_EXAMPLES ) ) ) ] ,
        [ 'enumerations'      , classesLabel( $src->count( 'xyz' , 'enumerations' ) , $lang ) ] ,
        [ 'traits'            , sprintf( $L['mixins'] , $src->count( 'xyz' , 'traits' , true , 'trait' ) ) ] ,
        [ 'helpers'           , sprintf( $L['functions'] , $src->functionsIn( 'xyz' , 'helpers' ) , implode( ' · ' , $helperDirs ) ) ] ,
    ] ;

    // -- org : the vocabulary
    $vocabulary = [] ;
    foreach ( $m->orgVocabulary as $top => $count )
    {
        $desc = classesLabel( $count , $lang ) ;
        if ( $top === '' )
        {
            $desc .= ' · ' . implode( ', ' , $src->existing( 'org' , ORG_ROOT_EXAMPLES ) ) . '…' ;
        }
        $vocabulary[] = [ $top === '' ? $L['root'] : $top , $desc ] ;
    }
    $orgTotal = array_sum( $m->orgVocabulary ) ;

    // -- org : the core
    $describe = static function( ?ClassInfo $c ) : string
    {
        if ( $c === null )
        {
            return '' ;
        }
        $parts = [] ;
        if ( $c->implements !== [] )
        {
            $parts[] = 'implements ' . implode( ', ' , $c->implements ) ;
        }
        if ( $c->traits !== [] )
        {
            $parts[] = implode( ' + ' , $c->traits ) ;
        }
        return implode( ' · ' , $parts ) ;
    } ;
    $helperClasses = implode( ' + ' , array_map( static fn( ClassInfo $c ) => $c->name , $src->in( 'org' , 'helpers' ) ) ) ;
    $core =
    [
        [ 'Thing' , $describe( $src->find( 'org' , 'Thing' ) ) ] ,
        [ $src->find( 'org' , 'Prop' ) !== null ? 'constants\\Schema ≡ Prop' : 'constants\\Schema' ,
          sprintf( $L['coreSchema'] , $src->count( 'org' , 'constants' , true , 'trait' ) , implode( ', ' , $src->existing( 'org' , ORG_CONSTANT_EXAMPLES ) ) ) ] ,
        [ 'traits'  , sprintf( $L['coreTraits'] , $src->count( 'org' , 'traits' , true , 'trait' ) , implode( ', ' , $src->existing( 'org' , ORG_TRAIT_EXAMPLES ) ) ) ] ,
        [ 'helpers' , sprintf( $L['coreHelpers'] , $helperClasses , $src->functionsIn( 'org' , 'helpers' ) ) ] ,
    ] ;
    if ( ( $dublin = $src->find( 'org' , 'DublinCore' ) ) !== null )
    {
        $core[] = [ 'DublinCore' , sprintf( $L['coreApart'] , str_replace( 'implements ' , '' , $describe( $dublin ) ) ) ] ;
    }

    // -- the foundations, read from composer.json
    $composer    = json_decode( (string) file_get_contents( $src->projectDir . '/composer.json' ) , true ) ;
    $require     = is_array( $composer ) && isset( $composer['require'] ) && is_array( $composer['require'] ) ? $composer['require'] : [] ;
    $foundations = [] ;
    foreach ( $require as $package => $version )
    {
        if ( $package === 'php' )
        {
            $foundations[] = [ 'PHP ' . str_replace( '>=' , '≥ ' , (string) $version ) , '' ] ;
        }
        else
        {
            $foundations[] = [ (string) $package , PACKAGE_NOTES[ $package ] ?? '' ] ;
        }
    }

    $contexts = [] ;
    foreach ( ROOTS as $root => $spec )
    {
        $contexts[ $root ] = $src->constant( $root , $spec['context'][0] , $spec['context'][1] ) ?? '' ;
    }

    // -- drawing
    $parts = [] ; $y = 0.0 ; $ax = 120 ;

    [ $s , $h ] = band( $y , ROOTS['xyz']['ns'] , sprintf( $L['domains'] , $xyzTotal ) , '@context ' . $contexts['xyz'] , $domains , 'xyz' ) ;
    $parts[] = $s ; $y += $h + 10 ;
    [ $s , $h ] = band( $y , ROOTS['xyz']['ns'] , $L['base'] , '' , $base , 'xyz' ) ;
    $parts[] = $s ; $y += $h ;

    // The seam between the two packages : the arrow never points up.
    $seam    = 72 ;
    $parts[] = svgLine( $ax , $y + 8 , $ax , $y + $seam - 8 , 'edge' , 'arr1' ) ;
    $parts[] = svgText( $ax + 14 , $y + 30 , $L['seam'] , 'sans label' ) ;
    $parts[] = svgText( $ax + 14 , $y + 46 , $m->orgImportsXyz === 0 ? $L['seamNone'] : sprintf( $L['seamSome'] , $m->orgImportsXyz ) , 'sans label muted' ) ;
    $y += $seam ;

    [ $s , $h ] = band( $y , ROOTS['org']['ns'] , sprintf( $L['vocabulary'] , $orgTotal ) , '@context ' . $contexts['org'] , $vocabulary , 'org' ) ;
    $parts[] = $s ; $y += $h + 10 ;
    [ $s , $h ] = band( $y , ROOTS['org']['ns'] , $L['core'] , '' , $core , 'org' ) ;
    $parts[] = $s ; $y += $h ;

    $gap     = 54 ;
    $parts[] = svgLine( $ax , $y + 8 , $ax , $y + $gap - 8 , 'edge' , 'arr1' ) ;
    $parts[] = svgText( $ax + 14 , $y + 31 , $L['reflect'] , 'sans label' ) ;
    $y += $gap ;

    [ $s , $h ] = band( $y , $L['foundations'] , $L['foundationsRole'] , '' , $foundations , 'neutral' ) ;
    $parts[] = $s ; $y += $h ;

    return svgDocument( $parts , $y , $L['aria1'] , svgMarker( 'arr1' , 'mk' ) ) ;
}

// ---------------------------------------------------------------------------
// Figure 2 — the anchors
// ---------------------------------------------------------------------------

/**
 * Wraps runs of leaves into lines of [ text , class ] tokens ; a domain tag stays with the last name of its run.
 * @param  list<array{names: list<string>, domain: string}> $runs
 * @return list<list<array{0: string, 1: string}>>
 */
function wrapLeaves( array $runs , float $maxWidth ) : array
{
    $lines = [] ; $line = [] ; $x = 0.0 ;

    $push = static function( string $token , string $class , float $w ) use ( &$lines , &$line , &$x , $maxWidth ) : void
    {
        if ( $line !== [] && $x + $w > $maxWidth )
        {
            $lines[] = $line ; $line = [] ; $x = 0.0 ;
        }
        $line[] = [ $token , $class ] ;
        $x     += $w ;
    } ;

    $last = count( $runs ) - 1 ;
    foreach ( $runs as $i => $run )
    {
        $tag  = ' · ' . $run['domain'] . ( $i < $last ? '    ' : '' ) ;
        $tagW = sansW( $tag , 11 ) ;
        $n    = count( $run['names'] ) ;
        foreach ( $run['names'] as $j => $name )
        {
            $isLast = $j === $n - 1 ;
            $token  = $name . ( $isLast ? '' : ', ' ) ;
            $w      = monoW( $token , 11.5 ) ;
            if ( $isLast && $line !== [] && $x + $w + $tagW > $maxWidth )
            {
                $lines[] = $line ; $line = [] ; $x = 0.0 ;
            }
            $push( $token , 'leaf' , $w ) ;
        }
        $push( $tag , 'tag' , $tagW ) ;
    }
    if ( $line !== [] )
    {
        $lines[] = $line ;
    }
    return $lines ;
}

/**
 * Groups leaves into runs by domain, in the order the leaves come.
 * @param  list<array{name: string, domain: string}> $leaves
 * @return list<array{names: list<string>, domain: string}>
 */
function runsByDomain( array $leaves , string $lang ) : array
{
    $runs = [] ;
    foreach ( $leaves as $leaf )
    {
        $domain = domainLabel( $leaf['domain'] , $lang ) ;
        if ( $runs !== [] && $runs[ count( $runs ) - 1 ]['domain'] === $domain )
        {
            $runs[ count( $runs ) - 1 ]['names'][] = $leaf['name'] ;
        }
        else
        {
            $runs[] = [ 'names' => [ $leaf['name'] ] , 'domain' => $domain ] ;
        }
    }
    return $runs ;
}

function renderAnchors( Model $m , string $lang ) : string
{
    $L        = labels( $lang ) ;
    $sections = LABELS[ $lang ]['sections'] ;

    // Anchors by section : the declared order first, the unlisted anchors at the end by size.
    $byName = [] ;
    foreach ( $m->anchors as $fqcn => $anchor )
    {
        $byName[ $anchor['name'] ][] = $fqcn ;
    }
    $placed  = [] ;
    $ordered = [] ;
    foreach ( ANCHOR_SECTIONS as $section => $names )
    {
        foreach ( $names as $name )
        {
            foreach ( $byName[ $name ] ?? [] as $fqcn )
            {
                $ordered[ $section ][] = $fqcn ;
                $placed[ $fqcn ]       = true ;
            }
        }
    }
    $others = array_diff_key( $m->anchors , $placed ) ;
    uasort( $others , static fn( array $a , array $b ) => [ $b['size'] , $a['name'] ] <=> [ $a['size'] , $b['name'] ] ) ;
    foreach ( $others as $fqcn => $anchor )
    {
        $ordered['others'][] = $fqcn ;
        $m->src->notices[]   = "Anchor {$fqcn} has no section in ANCHOR_SECTIONS : drawn under '{$sections['others']}'." ;
    }

    $col1R = 236 ; $col2X = 300 ; $col3X = 480 ; $col3W = WIDTH - $col3X ; $lh = 15 ;

    $o   = [] ;
    $o[] = svgText( 0 , 14 , $L['col1'] , 'sans colhead' ) ;
    $o[] = svgText( $col2X , 14 , $L['col2'] , 'sans colhead' ) ;
    $o[] = svgText( $col3X , 14 , $L['col3'] , 'sans colhead' ) ;
    $o[] = svgText( WIDTH , 14 , $L['extends'] , 'sans colhead' , 'end' ) ;

    $y = 30.0 ;
    foreach ( $ordered as $section => $fqcns )
    {
        $title = mb_strtoupper( $sections[ $section ] ) ;
        $y    += 18 ;
        $o[]   = svgText( 0 , $y , $title , 'sans eyebrow' ) ;
        $o[]   = svgLine( mb_strlen( $title ) * 10.5 * 0.78 + 14 , $y - 4 , WIDTH , $y - 4 , 'rule' ) ;
        $y    += 14 ;

        foreach ( $fqcns as $fqcn )
        {
            $anchor  = $m->anchors[ $fqcn ] ;
            $groups  = [] ;   // [ bridge name or '' , runs ]
            foreach ( $anchor['bridges'] as $bridge => $leaves )
            {
                $groups[] = [ $bridge , runsByDomain( $leaves , $lang ) ] ;
            }
            if ( $anchor['direct'] !== [] )
            {
                $groups[] = [ '' , runsByDomain( $anchor['direct'] , $lang ) ] ;
            }

            $y0      = $y ;
            $centers = [] ;
            foreach ( $groups as [ $bridge , $runs ] )
            {
                $lines = wrapLeaves( $runs , $col3W ) ;
                $gh    = max( 1 , count( $lines ) ) * $lh ;
                $cy    = $y + $gh / 2 ;
                $centers[] = [ $cy , $bridge ] ;
                foreach ( $lines as $i => $line )
                {
                    $spans = '' ;
                    foreach ( $line as $k => [ $text , $class ] )
                    {
                        $spans .= '<tspan class="' . $class . '">' . esc( $k === 0 ? ltrim( $text ) : $text ) . '</tspan>' ;
                    }
                    $o[] = '<text class="mono leaves" x="' . $col3X . '" y="' . fmt( $y + $i * $lh + 12 ) . '">' . $spans . '</text>' ;
                }
                if ( $bridge !== '' )
                {
                    $bw  = monoW( $bridge , 11.5 ) + 22 ;
                    $o[] = svgChip( $col2X , $cy - 11 , $bw , 22 , $bridge , 'xyz' ) ;
                    $o[] = svgLine( $col2X + $bw , $cy , $col3X - 8 , $cy , 'edge' , 'arr2' ) ;
                }
                $y += $gh + 5 ;
            }

            if ( $centers === [] )
            {
                $m->src->notices[] = "Anchor {$fqcn} has nothing to draw." ;
                continue ;
            }

            $blockCy = ( $y0 + $y - 5 ) / 2 ;
            $aw      = monoW( $anchor['name'] , 11.5 ) + 22 ;
            $o[]     = svgChip( $col1R - $aw , $blockCy - 11 , $aw , 22 , $anchor['name'] , 'org' ) ;

            if ( count( $centers ) === 1 )
            {
                [ $cy , $bridge ] = $centers[0] ;
                $o[] = svgLine( $col1R , $blockCy , ( $bridge !== '' ? $col2X : $col3X ) - 8 , $cy , $bridge !== '' ? 'edge' : 'edge direct' , $bridge !== '' ? 'arr2' : 'arr2m' ) ;
            }
            else
            {
                $sx  = $col1R + 26 ;
                $ys  = array_map( static fn( array $c ) => $c[0] , $centers ) ;
                $o[] = svgLine( $col1R , $blockCy , $sx , $blockCy , 'edge' ) ;
                $o[] = svgLine( $sx , min( $ys ) , $sx , max( $ys ) , 'edge' ) ;
                foreach ( $centers as [ $cy , $bridge ] )
                {
                    $o[] = svgLine( $sx , $cy , ( $bridge !== '' ? $col2X : $col3X ) - 8 , $cy , $bridge !== '' ? 'edge' : 'edge direct' , $bridge !== '' ? 'arr2' : 'arr2m' ) ;
                }
            }
            $y += 9 ;
        }
    }

    if ( $m->noParent !== [] )
    {
        $y   += 14 ;
        $list = [] ;
        foreach ( runsByDomain( $m->noParent , $lang ) as $run )
        {
            $list[] = implode( ', ' , $run['names'] ) . ' · ' . $run['domain'] ;
        }
        $o[] = svgText( 0 , $y , sprintf( $L['noParent'] , implode( '   ' , $list ) ) , 'sans label muted' ) ;
        $y  += 4 ;
    }

    return svgDocument( $o , $y + 8 , sprintf( $L['aria2'] , count( $m->anchors ) ) , svgMarker( 'arr2' , 'mk' ) . svgMarker( 'arr2m' , 'mk muted' ) ) ;
}

// ---------------------------------------------------------------------------
// Figure 3 — the couplings
// ---------------------------------------------------------------------------

/** The point where the segment from ( $cx , $cy ) to ( $tx , $ty ) leaves a box centered on the first point. @return array{0: float, 1: float} */
function clipToBox( float $cx , float $cy , float $tx , float $ty , float $w , float $h , float $margin = 2 ) : array
{
    $dx = $tx - $cx ; $dy = $ty - $cy ;
    $sx = $dx !== 0.0 ? ( $w / 2 + $margin ) / abs( $dx ) : INF ;
    $sy = $dy !== 0.0 ? ( $h / 2 + $margin ) / abs( $dy ) : INF ;
    $s  = min( $sx , $sy ) ;
    return [ $cx + $dx * $s , $cy + $dy * $s ] ;
}

function strokeWidth( int $n ) : float
{
    return match ( true ) { $n <= 1 => 1.1 , $n === 2 => 1.6 , $n === 3 => 2.0 , $n <= 5 => 2.4 , default => 3.0 } ;
}

function renderCoupling( Model $m , string $lang ) : string
{
    $L  = labels( $lang ) ;
    $NW = 176 ; $NH = 44 ;
    $colX = [ 200 , 460 , 720 , 980 ] ;
    $rowY = static fn( int $r ) => 80 + 190 * $r ;

    // Which domains take part : every domain of the layer, linked or not.
    $linked = [] ;
    foreach ( $m->edges as $a => $targets )
    {
        $linked[ $a ] = true ;
        foreach ( array_keys( $targets ) as $b )
        {
            $linked[ $b ] = true ;
        }
    }
    $isolated = [] ;
    $cells    = [] ;   // domain => [ col , row ]
    $maxRow   = max( array_map( static fn( array $c ) => $c[1] , COUPLING_GRID ) ) ;
    $unplaced = [] ;
    foreach ( array_keys( $m->xyzDomains ) as $key )
    {
        if ( !isset( $linked[ $key ] ) )
        {
            $isolated[] = domainLabel( $key , $lang ) ;
        }
        elseif ( isset( COUPLING_GRID[ $key ] ) )
        {
            $cells[ $key ] = COUPLING_GRID[ $key ] ;
        }
        else
        {
            $unplaced[] = $key ;
        }
    }
    $col = 0 ;
    foreach ( $unplaced as $key )
    {
        if ( $col === count( $colX ) )
        {
            $col = 0 ; $maxRow++ ;
        }
        $cells[ $key ]      = [ $col++ , $maxRow + 1 ] ;
        $m->src->notices[]  = "Domain '{$key}' has no cell in COUPLING_GRID : drawn on an extra row." ;
    }
    if ( $unplaced !== [] )
    {
        $maxRow++ ;
    }

    $center = static fn( string $key ) : array => [ $colX[ $cells[ $key ][0] ] ?? $colX[ count( $colX ) - 1 ] , $rowY( $cells[ $key ][1] ) ] ;

    $o   = [] ;
    $o[] = svgText( WIDTH , 16 , $L['legend3'] , 'sans colhead' , 'end' ) ;

    // -- the edges
    $mutual = $m->mutualPairs() ;
    $mutualIndex = [] ;
    foreach ( $mutual as $i => $pair )
    {
        $mutualIndex[ $pair['a'] . '|' . $pair['b'] ] = $i + 1 ;
    }
    foreach ( $m->edges as $a => $targets )
    {
        foreach ( $targets as $b => $refs )
        {
            if ( isset( $m->edges[ $b ][ $a ] ) )
            {
                continue ;   // drawn once, as a mutual edge, below
            }
            [ $ax , $ay ] = $center( $a ) ; [ $bx , $by ] = $center( $b ) ;
            $width = 'stroke-width:' . fmt( strokeWidth( count( $refs ) ) ) ;
            $dc = $cells[ $b ][0] - $cells[ $a ][0] ; $dr = $cells[ $b ][1] - $cells[ $a ][1] ;
            if ( $dr === 0 && abs( $dc ) >= 2 )
            {
                // Same row, a node in between : bow under the row (over it on the last row).
                $side = $cells[ $a ][1] === $maxRow ? -1 : 1 ;
                $sy   = $ay + $side * ( $NH / 2 + 2 ) ;
                $o[]  = '<path class="edge" style="' . $width . '" d="M' . fmt( $ax + 30 * ( $dc > 0 ? 1 : -1 ) ) . ',' . fmt( $sy )
                      . ' Q' . fmt( ( $ax + $bx ) / 2 ) . ',' . fmt( $ay + $side * 130 ) . ' ' . fmt( $bx - 30 * ( $dc > 0 ? 1 : -1 ) ) . ',' . fmt( $sy ) . '" marker-end="url(#arr3)"/>' ;
                continue ;
            }
            if ( $dc === 0 && abs( $dr ) >= 2 )
            {
                // Same column, a node in between : bow to the right (to the left on the last column).
                $side = $cells[ $a ][0] === count( $colX ) - 1 ? -1 : 1 ;
                $sx   = $ax + $side * ( $NW / 2 + 2 ) ;
                $o[]  = '<path class="edge" style="' . $width . '" d="M' . fmt( $sx ) . ',' . fmt( $ay + 12 * ( $dr > 0 ? 1 : -1 ) )
                      . ' Q' . fmt( $ax + $side * 120 ) . ',' . fmt( ( $ay + $by ) / 2 ) . ' ' . fmt( $sx ) . ',' . fmt( $by - 12 * ( $dr > 0 ? 1 : -1 ) ) . '" marker-end="url(#arr3)"/>' ;
                continue ;
            }
            [ $x1 , $y1 ] = clipToBox( $ax , $ay , $bx , $by , $NW , $NH ) ;
            [ $x2 , $y2 ] = clipToBox( $bx , $by , $ax , $ay , $NW , $NH ) ;
            $o[] = svgLine( $x1 , $y1 , $x2 , $y2 , 'edge' , 'arr3' , false , $width ) ;
        }
    }
    foreach ( $mutual as $i => $pair )
    {
        [ $ax , $ay ] = $center( $pair['a'] ) ; [ $bx , $by ] = $center( $pair['b'] ) ;
        [ $x1 , $y1 ] = clipToBox( $ax , $ay , $bx , $by , $NW , $NH ) ;
        [ $x2 , $y2 ] = clipToBox( $bx , $by , $ax , $ay , $NW , $NH ) ;
        $o[] = svgLine( $x1 , $y1 , $x2 , $y2 , 'edge mutual' , 'arr3x' , true , 'stroke-width:' . fmt( strokeWidth( $pair['size'] ) ) ) ;
        // The badge sits at 35 % of the way, off the line, clear of the crossings at the middle.
        $mx = $x1 + ( $x2 - $x1 ) * 0.35 ; $my = $y1 + ( $y2 - $y1 ) * 0.35 ;
        $dx = $x2 - $x1 ; $dy = $y2 - $y1 ; $len = sqrt( $dx * $dx + $dy * $dy ) ;
        [ $nx , $ny ] = abs( $dy ) < 1 ? [ 0.0 , -1.0 ] : [ -$dy / $len , $dx / $len ] ;
        $o[] = '<circle class="badge" cx="' . fmt( $mx + $nx * 14 ) . '" cy="' . fmt( $my + $ny * 14 ) . '" r="9"/>' ;
        $o[] = svgText( $mx + $nx * 14 , $my + $ny * 14 + 4 , (string) ( $i + 1 ) , 'sans badge-text' , 'middle' ) ;
    }

    // -- the nodes
    foreach ( $cells as $key => $cell )
    {
        [ $cx , $cy ] = $center( $key ) ;
        $o[] = '<rect class="node" x="' . fmt( $cx - $NW / 2 ) . '" y="' . fmt( $cy - $NH / 2 ) . '" width="' . $NW . '" height="' . $NH . '" rx="4"/>' ;
        $o[] = svgText( $cx - $NW / 2 + 12 , $cy - 3 , domainLabel( $key , $lang ) , 'mono name xyz-ink' ) ;
        $o[] = svgText( $cx - $NW / 2 + 12 , $cy + 13 , classesLabel( $m->xyzDomains[ $key ] , $lang ) . ( in_array( $key , $unplaced , true ) ? ' · ' . $L['unplaced'] : '' ) , 'sans desc xyz-ink' ) ;
    }
    if ( $isolated !== [] )
    {
        $cx = $colX[ COUPLING_ISOLATED_CELL[0] ] ; $cy = $rowY( COUPLING_ISOLATED_CELL[1] ) ;
        $o[] = '<rect class="node iso" x="' . fmt( $cx - $NW / 2 ) . '" y="' . fmt( $cy - $NH / 2 ) . '" width="' . $NW . '" height="' . $NH . '" rx="4"/>' ;
        $o[] = svgText( $cx - $NW / 2 + 12 , $cy - 3 , implode( ' · ' , $isolated ) , 'mono name muted' ) ;
        $o[] = svgText( $cx - $NW / 2 + 12 , $cy + 13 , $L['isolated'] , 'sans desc muted' ) ;
    }

    // -- the footnote : every mutual reference, class by class
    $y = $rowY( $maxRow ) + $NH / 2 + 36 ;
    if ( $mutual !== [] )
    {
        $o[] = svgLine( 24 , $y , WIDTH - 24 , $y , 'rule' ) ;
        $y  += 22 ;
        $o[] = svgText( 24 , $y , $L['mutualTitle'] , 'sans foot-title muted' ) ;
        $y  += 16 ;
        $maxChars = (int) floor( ( WIDTH - 24 - 60 ) / ( 10.5 * 0.62 ) ) ;
        foreach ( $mutual as $i => $pair )
        {
            $a = domainLabel( $pair['a'] , $lang ) ; $b = domainLabel( $pair['b'] , $lang ) ;
            $o[] = '<circle class="badge" cx="33" cy="' . fmt( $y + 5 ) . '" r="9"/>' ;
            $o[] = svgText( 33 , $y + 9 , (string) ( $i + 1 ) , 'sans badge-text' , 'middle' ) ;
            $o[] = svgText( 52 , $y + 9 , $a . ' ⇄ ' . $b , 'mono chiptext xyz-ink' ) ;
            $y  += 19 ;
            foreach ( [ [ $pair['a'] , $pair['b'] , $a , $b ] , [ $pair['b'] , $pair['a'] , $b , $a ] ] as [ $from , $to , $fromLabel , $toLabel ] )
            {
                $text  = $fromLabel . ' → ' . $toLabel . ' : ' . implode( ', ' , $m->edges[ $from ][ $to ] ) ;
                foreach ( explode( "\n" , wordwrap( $text , $maxChars , "\n" , true ) ) as $k => $line )
                {
                    $o[] = svgText( $k === 0 ? 52 : 52 + monoW( $fromLabel . ' → ' . $toLabel . ' : ' , 10.5 ) , $y + 6 , $line , 'mono foot muted' ) ;
                    $y  += 15 ;
                }
            }
            $y += 8 ;
        }
    }

    return svgDocument( $o , $y + 8 , sprintf( $L['aria3'] , count( $cells ) , count( $mutual ) ) , svgMarker( 'arr3' , 'mk' ) . svgMarker( 'arr3x' , 'mk xyz' ) ) ;
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

/** @var list<string> $argv */
$outDir = 'assets/images' ;
$langs  = array_keys( LABELS ) ;

foreach ( array_slice( $argv , 1 ) as $arg )
{
    if ( str_starts_with( $arg , '--out=' ) )
    {
        $outDir = substr( $arg , 6 ) ;
    }
    elseif ( str_starts_with( $arg , '--lang=' ) )
    {
        $langs = explode( ',' , substr( $arg , 7 ) ) ;
    }
    elseif ( $arg === '-h' || $arg === '--help' )
    {
        echo "Usage: php tools/generate-architecture-diagrams.php [--out=assets/images] [--lang=fr,en]\n" ;
        exit( 0 ) ;
    }
    else
    {
        fwrite( STDERR , "Unknown option: {$arg}\n" ) ;
        exit( 1 ) ;
    }
}

foreach ( $langs as $lang )
{
    if ( !isset( LABELS[ $lang ] ) )
    {
        fwrite( STDERR , "No labels for language '{$lang}' (known: " . implode( ', ' , array_keys( LABELS ) ) . ").\n" ) ;
        exit( 1 ) ;
    }
}

$projectDir = dirname( __DIR__ ) ;
$sources    = new Sources( $projectDir ) ;
$sources->scan() ;
$model      = new Model( $sources ) ;

$target = str_starts_with( $outDir , '/' ) ? $outDir : $projectDir . '/' . $outDir ;
if ( !is_dir( $target ) && !mkdir( $target , 0777 , true ) )
{
    fwrite( STDERR , "Cannot create {$target}\n" ) ;
    exit( 1 ) ;
}

$figures = [ 'layers' => 'renderLayers' , 'anchors' => 'renderAnchors' , 'coupling' => 'renderCoupling' ] ;
foreach ( $langs as $lang )
{
    foreach ( $figures as $figure => $renderer )
    {
        $svg  = $renderer( $model , $lang ) ;
        $file = "{$target}/architecture-{$figure}-{$lang}.svg" ;
        file_put_contents( $file , $svg ) ;
        preg_match( '/viewBox="0 0 (\d+) (\d+)"/' , $svg , $vb ) ;
        echo str_pad( $outDir . "/architecture-{$figure}-{$lang}.svg" , 44 ) . ( $vb[1] ?? '?' ) . '×' . ( $vb[2] ?? '?' ) . "\n" ;
    }
}

// -- a summary of what was measured, for the eye that compares two runs
echo "\n" ;
echo 'org\\schema         : ' . array_sum( $model->orgVocabulary ) . ' vocabulary classes, ' . $sources->count( 'org' , '' , true , 'trait' ) . ' traits, ' . $sources->functionsIn( 'org' , 'helpers' ) . " helper functions\n" ;
echo 'xyz\\oihana\\schema  : ' . array_sum( $model->xyzDomains ) . ' domain classes in ' . count( $model->xyzDomains ) . ' domains, ' . $sources->count( 'xyz' , 'enumerations' ) . ' enumerations, ' . $sources->count( 'xyz' , '' , true , 'trait' ) . ' traits, ' . $sources->functionsIn( 'xyz' , 'helpers' ) . " helper functions\n" ;
echo 'anchors            : ' . count( $model->anchors ) . " Schema.org types extended by the Oihana layer\n" ;
$links = 0 ;
foreach ( $model->edges as $targets )
{
    $links += count( $targets ) ;
}
echo 'couplings          : ' . $links . ' links between domains, ' . count( $model->mutualPairs() ) . ' mutual ('
   . implode( ', ' , array_map( static fn( array $p ) => str_replace( '/' , '\\' , $p['a'] ) . ' ⇄ ' . str_replace( '/' , '\\' , $p['b'] ) , $model->mutualPairs() ) ) . ")\n" ;

$notices = array_values( array_unique( $sources->notices ) ) ;
if ( $notices !== [] )
{
    fwrite( STDERR , "\n" . count( $notices ) . " notice(s):\n" ) ;
    foreach ( $notices as $notice )
    {
        fwrite( STDERR , "  - {$notice}\n" ) ;
    }
}
