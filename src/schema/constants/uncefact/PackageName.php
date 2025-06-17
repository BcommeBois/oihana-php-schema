<?php

namespace org\schema\constants\uncefact;

use org\schema\constants\properties\Prop;
use org\schema\PropertyValue;
use oihana\reflections\traits\ConstantsTrait;

/**
 * UN/CEFACT Package names (Recommendation 21).
 *
 * This class provides an extended selection of the most commonly used codes
 * across various commercial and logistical contexts.
 *
 * For an exhaustive list and official documentation, please consult:
 * @see https://unece.org/trade/uncefact/cl-recommendations
 * ```
 */
class PackageName
{
    use ConstantsTrait ;

    /**
     * A flexible container made of paper, plastic, or woven material, used for holding various goods (fr -> Sac, Sachet).
     */
    public const string BAG = 'Bag';

    /**
     * A general term for a long, rigid piece of material, often wood or metal,
     * used for structural support or as a flat surface (fr -> Barre, Lingot).
     */
    public const string BAR = 'Bar';

    /**
     * A rigid, cylindrical container, typically larger than a drum,
     * used for bulk liquids or solids (fr -> Fût, Baril).
     */
    public const string BARREL = 'Barrel';

    /**
     * A container typically made of interwoven material (like wicker or plastic strips),
     * often with handles, used for carrying or storing items (fr -> Panier).
     */
    public const string BASKET = 'Basket';

    /**
     * A large, rigid container, often cylindrical or rectangular,
     * used for bulk storage of loose materials like grain or waste
     * (fr -> Conteneur, Benne, Bac).
     */
    public const string BIN = 'Bin';

    /**
     * A flat, usually rectangular piece of stiff material (wood, cardboard, plastic)
     * used as a base or cover, or for structural support (fr -> Planche, Panneau).
     */
    public const string BOARD = 'Board';

    /**
     * A box is a general term for a container, typically rigid, with flat sides and often a lid.
     * The material can vary widely.
     */
    public const string BOX = 'Box' ;

    /**
     * A group of items typically bound together; a bunch or bundle (fr -> Faisceau, Botte).
     */
    public const string BUNCH = 'Bunch' ;

    /**
     * A collection of items or packages fastened or wrapped together; a packet (fr -> Paquet, Faisceau).
     */
    public const string BUNDLE = 'Bundle' ;
    /**
     * A large protective enclosure, typically made of bars or mesh,
     * used for transporting or storing animals or fragile goods (fr -> Cage).
     */
    public const string CAGE = 'Cage';

    /**
     * A container designed for liquids, typically rectangular,
     * with a relatively small volume (fr -> Boîte rectangulaire, Bidon).
     */
    public const string CAN_RECTANGULAR = 'Can, rectangular';

    /**
     * A container designed for liquids, typically cylindrical,
     * with a relatively small volume (fr -> Boîte cylindrique, Bidon).
     */
    public const string CAN_CYLINDRICAL = 'Can, cylindrical';

    /**
     * A cylindrical or rectangular container with a handle and a spout,
     * designed for pouring liquids (fr -> Bidon avec poignée et bec verseur).
     */
    public const string CAN_WITH_HANDLE_AND_SPOUT = 'Can, with handle and spout';

    /**
     * A large, narrow-necked bottle without external protection (fr -> Tourie non protégée).
     */
    public const string CARBOY_NON_PROTECTED = 'Carboy, non-protected';

    /**
     * A large, narrow-necked bottle with external protection,
     * often in a crate (fr -> Tourie protégée).
     */
    public const string CARBOY_PROTECTED = 'Carboy, protected';

    /**
     * A flat piece of paperboard or plastic used as a backing or for display packaging,
     * often for individual items (fr -> Carte, Carton, Support blister).
     */
    public const string CARD = 'Card' ;

    /**
     * A folding box made from corrugated or solid fibreboard,
     * commonly used for packaging (fr -> Carton).
     */
    public const string CARTON = 'Carton';

    /**
     * Cartridge (fr -> Cartouche).
     * Code: CQ, Numeric code: 92 [30, 40]
     */
    public const string CARTRIDGE = 'Cartridge' ;

    /**
     * A container, often made of wood or heavy cardboard,
     * used for packing goods; typically stronger than a box (fr -> Caisse).
     */
    public const string CASE = 'Case';

    /**
     * A type of case designed to maintain
     * a consistent temperature for its contents (fr -> Caisse isotherme).
     */
    public const string CASE_ISOTHERMIC = 'Case, isothermic'; // New entry

    /**
     * A container, often made of wood, consisting only of a framework without solid sides,
     * allowing contents to be visible (fr -> Caisse à claire-voie, Caisse squelette).
     */
    public const string CASE_SKELETON = 'Case, skeleton'; // New entry

    /**
     * A case made of steel (fr -> Caisse en acier).
     */
    public const string CASE_STEEL = 'Case, steel'; // New entry

    /**
     * A case that incorporates a pallet base for easier handling with forklifts (fr -> Caisse-palette).
     */
    public const string CASE_WITH_PALLET_BASE = 'Case, with pallet base'; // New entry

    /**
     * A case with a pallet base, made of cardboard (fr -> Caisse-palette en carton).
     */
    public const string CASE_WITH_PALLET_BASE_CARDBOARD = 'Case, with pallet base, cardboard'; // New entry

    /**
     * A case with a pallet base, made of metal (fr -> Caisse-palette en métal).
     */
    public const string CASE_WITH_PALLET_BASE_METAL = 'Case, with pallet base, metal'; // New entry

    /**
     * A case with a pallet base, made of plastic (fr -> Caisse-palette en plastique).
     */
    public const string CASE_WITH_PALLET_BASE_PLASTIC = 'Case, with pallet base, plastic'; // New entry

    /**
     * A case with a pallet base, made of wood (fr -> Caisse-palette en bois).
     */
    public const string CASE_WITH_PALLET_BASE_WOODEN = 'Case, with pallet base, wooden';

    /**
     * A large, sturdy wooden barrel or keg,
     * often used for alcoholic beverages like wine or spirits (fr -> Barrique, Tonneau).
     */
    public const string CASK = 'Cask';

    /**
     * A sturdy container, often rectangular and made of wood or metal,
     * used for storage or transport of valuables (fr -> Coffre).
     */
    public const string CHEST = 'Chest';

    /**
     * A cylindrical container, typically made of wood or metal,
     * used for transporting milk or other liquids (fr -> Baratte).
     */
    public const string CHURN = 'Churn';

    /**
     * A rack specifically designed for hanging clothes (fr -> Portant à vêtements).
     */
    public const string CLOTHING_RACK = 'Rack, clothing hanger';

    /**
     * A wound length of wire, rope, or other flexible material,
     * often in a spiral shape (fr -> Bobine, Rouleau).
     */
    public const string COIL = 'Coil';

    /**
     * A protective lid or cover, often temporary, placed over a container or item (fr -> Couvercle, Housse).
     */
    public const string COVER = 'Cover';

    /**
     * A strong, open box or container, usually made of wooden slats, used for transporting fragile or heavy goods (fr -> Caisse à claire-voie, Cageot).
     */
    public const string CRATE = 'Crate';

    /**
     * A type of crate specifically designed for transporting beer bottles or cans (fr -> Caisse à bière).
     */
    public const string CRATE_BEER = 'Crate, beer';

    /**
     * A bulk crate made primarily of cardboard, for large quantities of goods (fr -> Grande caisse en carton).
     */
    public const string CRATE_BULK_CARDBOARD = 'Crate, bulk, cardboard';

    /**
     * A bulk crate made primarily of plastic, for large quantities of goods (fr -> Grande caisse en plastique).
     */
    public const string CRATE_BULK_PLASTIC = 'Crate, bulk, plastic';

    /**
     * A bulk crate made primarily of wood, for large quantities of goods (fr -> Grande caisse en bois).
     */
    public const string CRATE_BULK_WOODEN = 'Crate, bulk, wooden';

    /**
     * A crate reinforced with a frame, offering additional structural integrity (fr -> Caisse à claire-voie à cadre).
     */
    public const string CRATE_FRAMED = 'Crate, framed';

    /**
     * A crate specifically designed for transporting fruits (fr -> Caisse à fruits, Clayette).
     */
    public const string CRATE_FRUIT = 'Crate, fruit';

    /**
     * A crate specifically designed for transporting milk bottles or cartons (fr -> Caisse à lait).
     */
    public const string CRATE_MILK = 'Crate, milk';

    /**
     * A cardboard crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en carton).
     */
    public const string CRATE_MULTIPLE_LAYER_CARDBOARD = 'Crate, multiple layer, cardboard';

    /**
     * A plastic crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en plastique).
     */
    public const string CRATE_MULTIPLE_LAYER_PLASTIC = 'Crate, multiple layer, plastic';

    /**
     * A wooden crate designed with multiple layers for organized packing (fr -> Caisse multi-couches en bois).
     */
    public const string CRATE_MULTIPLE_LAYER_WOODEN = 'Crate, multiple layer, wooden';

    /**
     * A shallow crate, often used for produce or smaller items (fr -> Caisse peu profonde, Barquette).
     */
    public const string CRATE_SHALLOW = 'Crate, shallow';

    /**
     * A traditional container made from wicker or interwoven wood strips, often conical,
     * used for carrying fish (fr -> Nasse, Panier de pêcheur).
     */
    public const string CREEL = 'Creel';

    /**
     * A small open-topped container, typically for drinking, often with a handle (fr -> Tasse, Gobelet).
     */
    public const string CUP = 'Cup';

    /**
     * A large, cylindrical container, often metallic, used for storing or transporting liquids or gases in bulk (fr -> Citerne cylindrique).
     */
    public const string CYLINDRICAL_TANK = 'Tank, cylindrical';

    /**
     * A large cylindrical container, typically made of metal, plastic, or fibreboard, used for bulk liquids or powders (fr -> Fût).
     */
    public const string DRUM = 'Drum';

    /**
     * A drum made of aluminium (fr -> Fût en aluminium).
     */
    public const string DRUM_ALUMINIUM = 'Drum, aluminium';

    /**
     * A drum made of aluminium with a non-removable head (fr -> Fût en aluminium à tête non amovible).
     */
    public const string DRUM_ALUMINIUM_NON_REMOVABLE_HEAD = 'Drum, aluminium, non-removable head';

    /**
     * A drum made of aluminium with a removable head (fr -> Fût en aluminium à tête amovible).
     */
    public const string DRUM_ALUMINIUM_REMOVABLE_HEAD = 'Drum, aluminium, removable head';

    /**
     * A drum made of fibreboard (fr -> Fût en carton).
     */
    public const string DRUM_FIBRE = 'Drum, fibre';

    /**
     * A drum made of iron (fr -> Fût en fer).
     */
    public const string DRUM_IRON = 'Drum, iron';

    /**
     * A drum made of plastic (fr -> Fût en plastique).
     */
    public const string DRUM_PLASTIC = 'Drum, plastic';

    /**
     * A drum made of plastic with a non-removable head (fr -> Fût en plastique à tête non amovible).
     */
    public const string DRUM_PLASTIC_NON_REMOVABLE_HEAD = 'Drum, plastic, non-removable head';

    /**
     * A drum made of plastic with a removable head (fr -> Fût en plastique à tête amovible).
     */
    public const string DRUM_PLASTIC_REMOVABLE_HEAD = 'Drum, plastic, removable head';

    /**
     * A drum made of plywood (fr -> Fût en contreplaqué).
     */
    public const string DRUM_PLYWOOD = 'Drum, plywood';

    /**
     * A drum made of steel (fr -> Fût en acier).
     */
    public const string DRUM_STEEL = 'Drum, steel';

    /**
     * A drum made of wood (fr -> Fût en bois).
     */
    public const string DRUM_WOODEN = 'Drum, wooden';

    /**
     * A drum made of steel with a non-removable head (fr -> Fût en acier à tête non amovible).
     */
    public const string DRUM_STEEL_NON_REMOVABLE_HEAD = 'Drum, steel, non-removable head';

    /**
     * A drum made of steel with a removable head (fr -> Fût en acier à tête amovible).
     */
    public const string DRUM_STEEL_REMOVABLE_HEAD = 'Drum, steel, removable head';

    /**
     * A thin, flat paper or plastic container used for mailing letters or documents (fr -> Enveloppe).
     */
    public const string ENVELOPE = 'Envelope';

    /**
     * A steel thin, flat paper or plastic container used for mailing letters or documents
     * (fr -> Enveloppe en acier).
     */
    public const string ENVELOPE_STEEL = 'Envelope, steel';

    /**
     * A pack of photographic film (fr -> Paquet de film).
     */
    public const string FILM_PACK = 'Filmpack';

    /**
     * A small, cylindrical wooden barrel, traditionally used for beer or butter (fr -> Quartaut, Baril).
     */
    public const string FIRKIN = 'Firkin';

    /**
     * A structural support or framework, often made of wood or metal, used to give shape or stability (fr -> Cadre, Chassis).
     */
    public const string FRAME = 'Frame';

    /**
     * A large, heavy, typically horizontal structural beam or support (fr -> Poutre, Traverse).
     */
    public const string GIRDER = 'Girder';

    /**
     * Multiple girders, typically bound together for transport or storage (fr -> Poutres, en fagots/bottes/treillis).
     */
    public const string GIRDERS = 'Girders, in bundle/bunch/truss';

    /**
     * A container used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string JAR = 'Jar' ;

    /**
     * A single trunk or large branch of a tree after being cut,
     * typically for timber (fr -> Grume).
     */
    public const string LOG = 'Log';

    /**
     * Multiple logs,
     * typically bound together for transport or storage (fr -> Grumes, en fagots/bottes/treillis).
     */
    public const string LOGS = 'Logs, in bundle/bunch/truss';

    /**
     * A small rectangular box, typically made of cardboard, for holding matches (fr -> Boîte d'allumettes).
     */
    public const string MATCH_BOX = 'Match Box';

    /**
     * A set of containers that fit one inside the other, or a group of items housed together (fr -> Emboîtement, Jeu de boîtes).
     */
    public const string NEST = 'Nest';

    /**
     * Indicates that the packaging type is not available, not specified, or not applicable (fr -> Non disponible, Non spécifié).
     */
    public const string NOT_AVAILABLE = 'Not available' ;

    /**
     * A wrapped package, usually of small to medium size, prepared for mailing or shipping (fr -> Colis, Paquet).
     */
    public const string PACKAGE = 'Package';

    /**
     * A cylindrical container, typically metal, with a carrying handle, used for liquids like paint or chemicals (fr -> Seau).
     */
    public const string PAIL = 'Pail';

    /**
     * A wrapped package, usually of small to medium size, prepared for mailing or shipping (fr -> Colis, Paquet).
     * Note: 'PA' is also used for "Packet" in some contexts.
     */
    public const string PARCEL = 'Parcel';

    /**
     * A flat transport structure, often made of wood,
     * used to consolidate goods into a unit load for handling by forklifts (fr -> Palette).
     */
    public const string PALLET = 'Pallet';

    /**
     * A type of container that combines a pallet base with a box-like superstructure,
     * often collapsible or detachable (fr -> Caisse-palette).
     */
    public const string PALLET_BOX = 'Pallet, box';

    /**
     * A specific size of pallet, 80cm x 60cm (fr -> Palette 80x60).
     */
    public const string PALLET_80x60 = 'Pallet, modular, collars 80cms * 60cms';

    /**
     * A specific size of pallet, 80cm x 100cm (fr -> Palette 80x100).
     */
    public const string PALLET_80x100 = 'Pallet, modular, collars 80cms * 100cms';

    /**
     * A specific size of pallet, 80cm x 120cm,
     * often known as an Euro pallet (fr -> Palette 80x120, Palette Euro).
     */
    public const string PALLET_80x120 = 'Pallet, modular, collars 80cms * 120cms';

    /**
     * A pallet shrink, wrapped (fr -> Palette, rétractable, emballée)
     */
    public const string PALLET_SHRINK_WRAPPED = 'Pallet, shrink, wrapped';

    /**
     * A hollow cylindrical structure, often used for conveying liquids or gases (fr -> Tuyau, Conduit).
     */
    public const string PIPE = 'Pipe';

    /**
     * A container, typically with a handle and a spout, used for pouring liquids (fr -> Cruche).
     */
    public const string PITCHER = 'Pitcher';

    /**
     * A flat, elongated piece of timber or metal,
     * thicker than a board, used for flooring or construction (fr -> Planche, Madrier).
     */
    public const string PLANK = 'Plank';

    /**
     * Multiple planks, typically bound together
     * (fr -> Planches, en fagots/bottes/treillis).
     */
    public const string PLANKS = 'Planks, in bundle/bunch/truss';

    /**
     * A Transport plate. (fr -> Plaque de transport)
     */
    public const string PLATE = 'Plate' ;

    /**
     * A set of transport plate. (fr -> Plaque de transport)
     */
    public const string PLATES = 'Plates, in bundle/bunch/truss';

    /**
     * A container used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string POT = 'Pot' ;

    /**
     * A small bag or flexible container,
     * often sealed, used for holding small items or portions of products (fr -> Pochette, Sachet).
     */
    public const string POUCH = 'Pouch';

    /**
     * A framework with shelves, hooks, or bars for holding or displaying items (fr -> Étagère, Casier).
     */
    public const string RACK = 'Rack';

    /**
     * A large, rectangular container, often metallic,
     * used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string RECTANGULAR_TANK = 'Tank, rectangular';

    /**
     * A cylindrical object formed by winding a flexible material,
     * or the material itself in this form (fr -> Rouleau, Bobine).
     */
    public const string ROLL = 'Roll';

    /**
     * A thin, straight bar or stick, often metal (fr -> Tige).
     */
    public const string ROD = 'Rod';

    /**
     * Multiple rods, typically bound together (fr -> Tiges, en fagots/bottes/treillis).
     */
    public const string RODS = 'Rods, in bundle/bunch/truss';

    /**
     * A small bag or pouch, often heat-sealed, used for single servings or small quantities (fr -> Sachet).
     */
    public const string SACHET = 'Sachet' ;

    /**
     * A collection or group of distinct items or packages considered as a unit (fr -> Ensemble, Collection).
     */
    public const string SET = 'Set' ;

    /**
     * A single, flat, thin piece of material, often paper, plastic, or fabric (fr -> Feuille).
     */
    public const string SHEET = 'Sheet' ;

    /**
     * A single, flat piece of metal in sheet form (fr -> Tôle, Feuille métallique).
     */
    public const string SHEET_METAL = 'Sheetmetal' ;

    /**
     * Sheet, plastic wrapping (fr -> Feuille, emballage plastique)
     */
    public const string SHEET_PLASTIC_WRAPPING = 'Sheet, plastic wrapping' ;

    /**
     * Multiple sheets, typically bound together
     * for transport or storage (fr -> Feuilles, en fagots/bottes/treillis).
     */
    public const string SHEETS = 'Sheets, in bundle/bunch/truss' ;

    /**
     * Items or packages wrapped tightly in a thin plastic film
     * that shrinks when heat is applied (fr -> Emballé sous film plastique ou retractable).
     */
    public const string SHRINK_WRAPPED = 'Shrinkwrapped' ;

    /**
     * A thick, flat piece of a solid material.
     * It's a very versatile word, commonly used in various contexts.
     */
    public const string SLAB = 'Slab' ;

    /**
     * A tin (or tin can in American English) specifically refers to a container made of tinplate,
     * which is steel coated with a thin layer of tin.
     * This coating provides corrosion resistance and allows for easy soldering
     * (fr -> Boîte de conserve, Boîte en fer-blanc).
     */
    public const string TIN = 'Tin' ;

    /**
     * A small, rectangular container, often metallic,
     * used for storing or transporting liquids or gases in bulk (fr -> Citerne rectangulaire).
     */
    public const string TRAY = 'Tray' ;

    /**
     * A vat is a large container used for holding, mixing, or storing liquids or other substances,
     * especially in industrial or commercial processes (fr -> Cuve).
     */
    public const string VAT = 'Vat' ;

    // =====================================================================
    // Private
    // =====================================================================

    private static ?array $CODES = null ;

    // =====================================================================
    // Methods
    // =====================================================================

    /**
     * Returns the package definition (name, unitCode) of the specific name
     * or null if the name is not valid.
     * @param string $name The name of the unit.
     * @return ?PropertyValue
     */
    public static function get( string $name ): ?PropertyValue
    {
        if( self::isValid( $name ) )
        {
            return new PropertyValue
            ([
                Prop::NAME       => $name ,
                Prop::UNIT_CODE  => self::getCode( $name ) ,
            ] );
        }
        return null ;
    }

    /**
     * Returns the name with a specific unit code.
     * @param string $code
     * @return string|null
     */
    public static function getFromCode( string $code ): ?string
    {
        return PackageCode::getName( $code ) ;
    }

    /**
     * Returns the official UN/CEFACT code for a given code.
     * @param string $name
     * @return string|null The UN/CEFACT code or null if not found.
     */
    public static function getCode( string $name ): ?string
    {
        if( static::$CODES === null )
        {
            static::$CODES = PackageCode::getAll() ;
        }
        return static::$CODES[ self::getConstant( $name ) ] ?? null;
    }
}