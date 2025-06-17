<?php

namespace org\schema\constants\uncefact;

use oihana\reflections\traits\ConstantsTrait;
use org\schema\constants\properties\Prop;
use org\schema\PropertyValue;

/**
 * UN/CEFACT Package codes (Recommendation 21).
 *
 * This class provides an extended selection of the most commonly used codes
 * across various commercial and logistical contexts.
 *
 * For an exhaustive list and official documentation, please consult:
 * @see https://unece.org/trade/uncefact/cl-recommendations
 * ```
 */
class PackageCode
{
    use ConstantsTrait ;

    /**
     * A flexible container made of paper, plastic, or woven material, used for holding various goods (fr -> Sac, Sachet).
     */
    public const string BAG = 'BG';

    /**
     * A general term for a long, rigid piece of material, often wood or metal,
     * used for structural support or as a flat surface (fr -> Barre, Lingot).
     */
    public const string BAR = 'BR';

    /**
     * A rigid, cylindrical container, typically larger than a drum,
     * used for bulk liquids or solids (fr -> Fût, Baril).
     */
    public const string BARREL = 'BA';

    /**
     * A container typically made of interwoven material (like wicker or plastic strips),
     * often with handles, used for carrying or storing items (fr -> Panier).
     */
    public const string BASKET = 'BK';

    /**
     * A large, rigid container, often cylindrical or rectangular,
     * used for bulk storage of loose materials like grain or waste
     * (fr -> Conteneur, Benne, Bac).
     */
    public const string BIN = 'BI';

    /**
     * A flat, usually rectangular piece of stiff material (wood, cardboard, plastic)
     * used as a base or cover, or for structural support (fr -> Planche, Panneau).
     */
    public const string BOARD = 'BD';

    /**
     * A box is a general term for a container, typically rigid, with flat sides and often a lid.
     * The material can vary widely.
     */
    public const string BOX = 'BX' ;

    /**
     * A group of items typically bound together; a bunch or bundle (fr -> Faisceau, Botte).
     */
    public const string BUNCH = 'BH' ;

    /**
     * A collection of items or packages fastened or wrapped together; a packet (fr -> Paquet, Faisceau).
     */
    public const string BUNDLE = 'BE' ;
    /**
     * A large protective enclosure, typically made of bars or mesh,
     * used for transporting or storing animals or fragile goods (fr -> Cage).
     */
    public const string CAGE = 'CG';

    /**
     * A container designed for liquids, typically rectangular,
     * with a relatively small volume (fr -> Boîte rectangulaire, Bidon).
     */
    public const string CAN_RECTANGULAR = 'CA';

    /**
     * A container designed for liquids, typically cylindrical,
     * with a relatively small volume (fr -> Boîte cylindrique, Bidon).
     */
    public const string CAN_CYLINDRICAL = 'CX';

    /**
     * A cylindrical or rectangular container with a handle and a spout,
     * designed for pouring liquids (fr -> Bidon avec poignée et bec verseur).
     */
    public const string CAN_WITH_HANDLE_AND_SPOUT = 'CD';

    /**
     * A large, narrow-necked bottle without external protection (fr -> Tourie non protégée).
     */
    public const string CARBOY_NON_PROTECTED = 'CO';

    /**
     * A large, narrow-necked bottle with external protection,
     * often in a crate (fr -> Tourie protégée).
     */
    public const string CARBOY_PROTECTED = 'CP';

    /**
     * A flat piece of paperboard or plastic used as a backing or for display packaging,
     * often for individual items (fr -> Carte, Carton, Support blister).
     */
    public const string CARD = 'CM' ;

    /**
     * A folding box made from corrugated or solid fibreboard,
     * commonly used for packaging (fr -> Carton).
     */
    public const string CARTON = 'CT';

    /**
     * Cartridge (fr -> Cartouche).
     * Code: CQ, Numeric code: 92 [30, 40]
     */
    public const string CARTRIDGE = 'CQ';

    /**
     * A container, often made of wood or heavy cardboard,
     * used for packing goods; typically stronger than a box (fr -> Caisse).
     */
    public const string CASE = 'CS';

    /**
     * A type of case designed to maintain
     * a consistent temperature for its contents (fr -> Caisse isotherme).
     */
    public const string CASE_ISOTHERMIC = 'EI'; // New entry

    /**
     * A container, often made of wood, consisting only of a framework without solid sides,
     * allowing contents to be visible (fr -> Caisse à claire-voie, Caisse squelette).
     */
    public const string CASE_SKELETON = 'SK'; // New entry

    /**
     * A case made of steel (fr -> Caisse en acier).
     */
    public const string CASE_STEEL = 'SS'; // New entry

    /**
     * A case that incorporates a pallet base for easier handling with forklifts (fr -> Caisse-palette).
     */
    public const string CASE_WITH_PALLET_BASE = 'ED'; // New entry

    /**
     * A case with a pallet base, made of cardboard (fr -> Caisse-palette en carton).
     */
    public const string CASE_WITH_PALLET_BASE_CARDBOARD = 'EF'; // New entry

    /**
     * A case with a pallet base, made of metal (fr -> Caisse-palette en métal).
     */
    public const string CASE_WITH_PALLET_BASE_METAL = 'EH'; // New entry

    /**
     * A case with a pallet base, made of plastic (fr -> Caisse-palette en plastique).
     */
    public const string CASE_WITH_PALLET_BASE_PLASTIC = 'EG'; // New entry

    /**
     * A case with a pallet base, made of wood (fr -> Caisse-palette en bois).
     */
    public const string CASE_WITH_PALLET_BASE_WOODEN = 'EE';

    /**
     * A large, sturdy wooden barrel or keg,
     * often used for alcoholic beverages like wine or spirits (fr -> Barrique, Tonneau).
     */
    public const string CASK = 'CK';

    /**
     * A sturdy container, often rectangular and made of wood or metal,
     * used for storage or transport of valuables (fr -> Coffre).
     */
    public const string CHEST = 'CH';

    /**
     * A cylindrical container, typically made of wood or metal,
     * used for transporting milk or other liquids (fr -> Baratte).
     */
    public const string CHURN = 'CC';

    /**
     * A rack specifically designed for hanging clothes (fr -> Portant à vêtements).
     */
    public const string CLOTHING_RACK = 'RJ';

    /**
     * A wound length of wire, rope, or other flexible material,
     * often in a spiral shape (fr -> Bobine, Rouleau).
     */
    public const string COIL = 'CL';

    /**
     * A protective lid or cover, often temporary, placed over a container or item (fr -> Couvercle, Housse).
     */
    public const string COVER = 'CV';

    /**
     * A strong, open box or container, usually made of wooden slats, used for transporting fragile or heavy goods (fr -> Caisse à claire-voie, Cageot).
     */
    public const string CRATE = 'CR';

    /**
     * A type of crate specifically designed for transporting beer bottles or cans (fr -> Caisse à bière).
     */
    public const string CRATE_BEER = 'CB';

    /**
     * A bulk crate made primarily of cardboard, for large quantities of goods (fr -> Grande caisse en carton).
     */
    public const string CRATE_BULK_CARDBOARD = 'DK';

    /**
     * A bulk crate made primarily of plastic, for large quantities of goods (fr -> Grande caisse en plastique).
     */
    public const string CRATE_BULK_PLASTIC = 'DL';

    /**
     * A bulk crate made primarily of wood, for large quantities of goods (fr -> Grande caisse en bois).
     */
    public const string CRATE_BULK_WOODEN = 'DM';

    /**
     * A crate reinforced with a frame, offering additional structural integrity (fr -> Caisse à claire-voie à cadre).
     */
    public const string CRATE_FRAMED = 'FD';

    /**
     * A crate specifically designed for transporting fruits (fr -> Caisse à fruits, Clayette).
     */
    public const string CRATE_FRUIT = 'FC';

    /**
     * A crate specifically designed for transporting milk bottles or cartons (fr -> Caisse à lait).
     */
    public const string CRATE_MILK = 'MC';

    /**
     * A cardboard crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en carton).
     */
    public const string CRATE_MULTIPLE_LAYER_CARDBOARD = 'DC';

    /**
     * A plastic crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en plastique).
     */
    public const string CRATE_MULTIPLE_LAYER_PLASTIC = 'DA';

    /**
     * A wooden crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en bois).
     */
    public const string CRATE_MULTIPLE_LAYER_WOODEN = 'DB';

    /**
     * A shallow crate, often used for produce or smaller items (fr -> Caisse peu profonde, Barquette).
     */
    public const string CRATE_SHALLOW = 'SC';

    /**
     * A traditional container made from wicker or interwoven wood strips, often conical,
     * used for carrying fish (fr -> Nasse, Panier de pêcheur).
     */
    public const string CREEL = 'CE';

    /**
     * A small open-topped container, typically for drinking, often with a handle (fr -> Tasse, Gobelet).
     */
    public const string CUP = 'CU';

    /**
     * A large, cylindrical container, often metallic, used for storing or transporting liquids or gases in bulk (fr -> Citerne cylindrique).
     */
    public const string CYLINDRICAL_TANK = 'TY';

    /**
     * A large cylindrical container, typically made of metal, plastic, or fibreboard, used for bulk liquids or powders (fr -> Fût).
     */
    public const string DRUM = 'DR';

    /**
     * A drum made of aluminium (fr -> Fût en aluminium).
     */
    public const string DRUM_ALUMINIUM = '1B';

    /**
     * A drum made of aluminium with a non-removable head (fr -> Fût en aluminium à tête non amovible).
     */
    public const string DRUM_ALUMINIUM_NON_REMOVABLE_HEAD = 'QC';

    /**
     * A drum made of aluminium with a removable head (fr -> Fût en aluminium à tête amovible).
     */
    public const string DRUM_ALUMINIUM_REMOVABLE_HEAD = 'QD';

    /**
     * A drum made of fibreboard (fr -> Fût en carton).
     */
    public const string DRUM_FIBRE = '1G';

    /**
     * A drum made of iron (fr -> Fût en fer).
     */
    public const string DRUM_IRON = 'DI';

    /**
     * A drum made of plastic (fr -> Fût en plastique).
     */
    public const string DRUM_PLASTIC = 'IH';

    /**
     * A drum made of plastic with a non-removable head (fr -> Fût en plastique à tête non amovible).
     */
    public const string DRUM_PLASTIC_NON_REMOVABLE_HEAD = 'QF';

    /**
     * A drum made of plastic with a removable head (fr -> Fût en plastique à tête amovible).
     */
    public const string DRUM_PLASTIC_REMOVABLE_HEAD = 'QG';

    /**
     * A drum made of plywood (fr -> Fût en contreplaqué).
     */
    public const string DRUM_PLYWOOD = '1D';

    /**
     * A drum made of steel (fr -> Fût en acier).
     */
    public const string DRUM_STEEL = '1A';

    /**
     * A drum made of steel with a non-removable head (fr -> Fût en acier à tête non amovible).
     */
    public const string DRUM_STEEL_NON_REMOVABLE_HEAD = 'QA';

    /**
     * A drum made of steel with a removable head (fr -> Fût en acier à tête amovible).
     */
    public const string DRUM_STEEL_REMOVABLE_HEAD = 'QB';

    /**
     * A drum made of wood (fr -> Fût en bois).
     */
    public const string DRUM_WOODEN = '1W';

    /**
     * A thin, flat paper or plastic container used for mailing letters or documents (fr -> Enveloppe).
     */
    public const string ENVELOPE = 'EN';

    /**
     * A steel thin, flat paper or plastic container used for mailing letters or documents
     * (fr -> Enveloppe en acier).
     */
    public const string ENVELOPE_STEEL = 'SV';

    /**
     * A small, cylindrical wooden barrel, traditionally used for beer or butter (fr -> Quartaut, Baril).
     */
    public const string FIRKIN = 'FI';

    /**
     * A pack of photographic film (fr -> Paquet de film).
     */
    public const string FILM_PACK = 'FP';

    /**
     * A structural support or framework, often made of wood or metal, used to give shape or stability (fr -> Cadre, Chassis).
     */
    public const string FRAME = 'FR';

    /**
     * A large, heavy, typically horizontal structural beam or support (fr -> Poutre, Traverse).
     */
    public const string GIRDER = 'GI';

    /**
     * Multiple girders, typically bound together for transport or storage (fr -> Poutres, en fagots/bottes/treillis).
     */
    public const string GIRDERS = 'GZ';

    /**
     * A container used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string JAR = 'JR' ;

    /**
     * A single trunk or large branch of a tree after being cut,
     * typically for timber (fr -> Grume).
     */
    public const string LOG = 'LG';

    /**
     * Multiple logs,
     * typically bound together for transport or storage (fr -> Grumes, en fagots/bottes/treillis).
     */
    public const string LOGS = 'LZ';

    /**
     * A small rectangular box, typically made of cardboard, for holding matches (fr -> Boîte d'allumettes).
     */
    public const string MATCH_BOX = 'MX';

    /**
     * A set of containers that fit one inside the other, or a group of items housed together (fr -> Emboîtement, Jeu de boîtes).
     */
    public const string NEST = 'NS';

    /**
     * Indicates that the packaging type is not available, not specified, or not applicable (fr -> Non disponible, Non spécifié).
     */
    public const string NOT_AVAILABLE = 'NA' ;

    /**
     * A wrapped package, usually of small to medium size, prepared for mailing or shipping (fr -> Colis, Paquet).
     */
    public const string PACKAGE = 'PK';

    /**
     * A cylindrical container, typically metal, with a carrying handle, used for liquids like paint or chemicals (fr -> Seau).
     */
    public const string PAIL = 'PL';

    /**
     * A wrapped package, usually of small to medium size, prepared for mailing or shipping (fr -> Colis, Paquet).
     * Note: 'PA' is also used for "Packet" in some contexts.
     */
    public const string PARCEL = 'PA';

    /**
     * A flat transport structure, often made of wood,
     * used to consolidate goods into a unit load for handling by forklifts (fr -> Palette).
     */
    public const string PALLET = 'PX';

    /**
     * A type of container that combines a pallet base with a box-like superstructure,
     * often collapsible or detachable (fr -> Caisse-palette).
     */
    public const string PALLET_BOX = 'PB';

    /**
     * A specific size of pallet, 80cm x 60cm (fr -> Palette 80x60).
     */
    public const string PALLET_80x60 = 'AF';

    /**
     * A specific size of pallet, 80cm x 100cm (fr -> Palette 80x100).
     */
    public const string PALLET_80x100 = 'PD';

    /**
     * A specific size of pallet, 80cm x 120cm,
     * often known as an Euro pallet (fr -> Palette 80x120, Palette Euro).
     */
    public const string PALLET_80x120 = 'PE';

    /**
     * A pallet shrink, wrapped (fr -> Palette, rétractable, emballée)
     */
    public const string PALLET_SHRINK_WRAPPED = 'AG';

    /**
     * A hollow cylindrical structure, often used for conveying liquids or gases (fr -> Tuyau, Conduit).
     */
    public const string PIPE = 'PI';

    /**
     * A container, typically with a handle and a spout, used for pouring liquids (fr -> Cruche).
     */
    public const string PITCHER = 'PH';

    /**
     * A flat, elongated piece of timber or metal,
     * thicker than a board, used for flooring or construction (fr -> Planche, Madrier).
     */
    public const string PLANK = 'PN';

    /**
     * Multiple planks, typically bound together
     * (fr -> Planches, en fagots/bottes/treillis).
     */
    public const string PLANKS = 'PZ';

    /**
     * A Transport plate. (fr -> Plaque de transport)
     */
    public const string PLATE = 'PG' ;

    /**
     * A set of transport plate. (fr -> Plaque de transport)
     */
    public const string PLATES = 'PZ';

    /**
     * A small bag or flexible container,
     * often sealed, used for holding small items or portions of products (fr -> Pochette, Sachet).
     */
    public const string POUCH = 'PO';

    /**
     * A container used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string POT = 'PT' ;

    /**
     * A framework with shelves, hooks, or bars for holding or displaying items (fr -> Étagère, Casier).
     */
    public const string RACK = 'RK';

    /**
     * A large, rectangular container, often metallic,
     * used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string RECTANGULAR_TANK = 'TK';

    /**
     * A cylindrical object formed by winding a flexible material,
     * or the material itself in this form (fr -> Rouleau, Bobine).
     */
    public const string ROLL = 'RD';

    /**
     * A thin, straight bar or stick, often metal (fr -> Tige).
     */
    public const string ROD = 'RD';

    /**
     * Multiple rods, typically bound together (fr -> Tiges, en fagots/bottes/treillis).
     */
    public const string RODS = 'RZ';

    /**
     * A small bag or pouch, often heat-sealed, used for single servings or small quantities (fr -> Sachet).
     */
    public const string SACHET = 'SH' ;

    /**
     * A collection or group of distinct items or packages considered as a unit (fr -> Ensemble, Collection).
     */
    public const string SET = 'SM' ;

    /**
     * A single, flat, thin piece of material, often paper, plastic, or fabric (fr -> Feuille).
     */
    public const string SHEET = 'ST' ;

    /**
     * A single, flat piece of metal in sheet form (fr -> Tôle, Feuille métallique).
     */
    public const string SHEET_METAL = 'Sheetmetal' ;

    /**
     * Sheet, plastic wrapping (fr -> Feuille, emballage plastique)
     */
    public const string SHEET_PLASTIC_WRAPPING = 'SP' ;

    /**
     * Multiple sheets, typically bound together
     * for transport or storage (fr -> Feuilles, en fagots/bottes/treillis).
     */
    public const string SHEETS = 'SZ' ;

    /**
     * Items or packages wrapped tightly in a thin plastic film
     * that shrinks when heat is applied (fr -> Emballé sous film plastique ou retractable).
     */
    public const string SHRINK_WRAPPED = 'SW' ;

    /**
     * A thick, flat piece of a solid material.
     * It's a very versatile word, commonly used in various contexts.
     */
    public const string SLAB = 'SB' ;

    /**
     * A tin (or tin can in American English) specifically refers to a container made of tinplate,
     * which is steel coated with a thin layer of tin.
     * This coating provides corrosion resistance and allows for easy soldering
     * (fr -> Boîte de conserve, Boîte en fer-blanc).
     */
    public const string TIN = 'TN' ;

    /**
     * A small, rectangular container, often metallic,
     * used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string TRAY = 'PU' ;

    /**
     * A vat is a large container used for holding, mixing, or storing liquids or other substances,
     * especially in industrial or commercial processes (fr -> Cuve).
     */
    public const string VAT = 'VA' ;

    // =====================================================================
    // Private
    // =====================================================================

    private static ?array $NAMES   = null ;

    // =====================================================================
    // Methods
    // =====================================================================


    /**
     * Returns the definition (name and unitCode) of the specific package code.
     * or null if the code is not valid.
     * @param string $code
     * @return ?PropertyValue
     */
    public static function get( string $code ): ?PropertyValue
    {
        if( self::isValid( $code ) )
        {
            return new PropertyValue
            ([
                Prop::NAME      => self::getName( $code ) ,
                Prop::UNIT_CODE => $code ,
            ] );
        }
        return null ;
    }
    /**
     * Returns the code with a specific package code name.
     * @param string $name
     * @return string|null
     */
    public static function getFromName( string $name ): ?string
    {
        return PackageName::getCode( $name ) ;
    }

    /**
     * Returns the official UN/CEFACT name for a given package code.
     * @param string $code
     * @return string|null The UN/CEFACT name or null if not found.
     */
    public static function getName( string $code ): ?string
    {
        if( static::$NAMES === null )
        {
            static::$NAMES = PackageName::getAll() ;
        }
        return static::$NAMES[ self::getConstant( $code ) ] ?? null;
    }
}