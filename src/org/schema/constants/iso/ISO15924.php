<?php

namespace org\schema\constants\iso;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Class ISO15924
 *
 * Provides a set of constants representing script codes as defined by the ISO 15924 standard.
 *
 * The ISO 15924 standard defines four-letter codes for the representation of names of writing scripts
 * used in the world's writing systems. Each code consists of four letters, starting with an uppercase
 * letter followed by three lowercase letters (e.g., 'Latn' for Latin, 'Cyrl' for Cyrillic).
 *
 * These constants can be used to ensure consistency and correctness when working with multilingual text
 * processing, localization, or internationalization.
 *
 * Example usage:
 *   $script = ISO15924::ADLM; // 'Adlm'
 *
 * @see https://unicode.org/iso15924/iso15924-codes.html Official ISO 15924 code list
 */
class ISO15924
{
    use ConstantsTrait;

    /**
     * Adlam
     */
    public const string ADLM = 'Adlm';

    /**
     * Afaka
     */
    public const string AFAK = 'Afak';

    /**
     * Caucasian Albanian
     */
    public const string AGHB = 'Aghb';

    /**
     * Ahom, Tai Ahom
     */
    public const string AHOM = 'Ahom';

    /**
     * Arabic
     */
    public const string ARAB = 'Arab';

    /**
     * Arabic (Nastaliq variant)
     */
    public const string ARAN = 'Aran';

    /**
     * Imperial Aramaic
     */
    public const string ARMI = 'Armi';

    /**
     * Armenian
     */
    public const string ARMN = 'Armn';

    /**
     * Avestan
     */
    public const string AVST = 'Avst';

    /**
     * Balinese
     */
    public const string BALI = 'Bali';

    /**
     * Bamum
     */
    public const string BAMU = 'Bamu';

    /**
     * Bassa Vah
     */
    public const string BASS = 'Bass';

    /**
     * Batak
     */
    public const string BATK = 'Batk';

    /**
     * Bengali (Bangla)
     */
    public const string BENG = 'Beng';

    /**
     * Beria Erfe
     */
    public const string BERF = 'Berf';

    /**
     * Bhaiksuki
     */
    public const string BHKS = 'Bhks';

    /**
     * Blissymbols
     */
    public const string BLIS = 'Blis';

    /**
     * Bopomofo
     */
    public const string BOPO = 'Bopo';

    /**
     * Brahmi
     */
    public const string BRAH = 'Brah';

    /**
     * Braille
     */
    public const string BRAI = 'Brai';

    /**
     * Buginese
     */
    public const string BUGI = 'Bugi';

    /**
     * Buhid
     */
    public const string BUHD = 'Buhd';

    /**
     * Chakma
     */
    public const string CAKM = 'Cakm';

    /**
     * Unified Canadian Aboriginal Syllabics
     */
    public const string CANS = 'Cans';

    /**
     * Carian
     */
    public const string CARI = 'Cari';

    /**
     * Cham
     */
    public const string CHAM = 'Cham';

    /**
     * Cherokee
     */
    public const string CHER = 'Cher';

    /**
     * Chisoi
     */
    public const string CHIS = 'Chis';

    /**
     * Chorasmian
     */
    public const string CHRS = 'Chrs';

    /**
     * Cirth
     */
    public const string CIRT = 'Cirt';

    /**
     * Coptic
     */
    public const string COPT = 'Copt';

    /**
     * Cypro-Minoan
     */
    public const string CPMN = 'Cpmn';

    /**
     * Cypriot syllabary
     */
    public const string CPRT = 'Cprt';

    /**
     * Cyrillic
     */
    public const string CYRL = 'Cyrl';

    /**
     * Cyrillic (Old Church Slavonic variant)
     */
    public const string CYRS = 'Cyrs';

    /**
     * Devanagari (Nagari)
     */
    public const string DEVA = 'Deva';

    /**
     * Dives Akuru
     */
    public const string DIAK = 'Diak';

    /**
     * Dogra
     */
    public const string DOGR = 'Dogr';

    /**
     * Deseret (Mormon)
     */
    public const string DSRT = 'Dsrt';

    /**
     * Duployan shorthand, Duployan stenography
     */
    public const string DUPL = 'Dupl';

    /**
     * Egyptian demotic
     */
    public const string EGYD = 'Egyd';

    /**
     * Egyptian hieratic
     */
    public const string EGYH = 'Egyh';

    /**
     * Egyptian hieroglyphs
     */
    public const string EGYP = 'Egyp';

    /**
     * Elbasan
     */
    public const string ELBA = 'Elba';

    /**
     * Elymaic
     */
    public const string ELYM = 'Elym';

    /**
     * Ethiopic (Geʻez)
     */
    public const string ETHI = 'Ethi';

    /**
     * Garay
     */
    public const string GARA = 'Gara';

    /**
     * Khutsuri (Asomtavruli and Nuskhuri)
     */
    public const string GEOK = 'Geok';

    /**
     * Georgian (Mkhedruli and Mtavruli)
     */
    public const string GEOR = 'Geor';

    /**
     * Glagolitic
     */
    public const string GLAG = 'Glag';

    /**
     * Gunjala Gondi
     */
    public const string GONG = 'Gong';

    /**
     * Masaram Gondi
     */
    public const string GONM = 'Gonm';

    /**
     * Gothic
     */
    public const string GOTH = 'Goth';

    /**
     * Grantha
     */
    public const string GRAN = 'Gran';

    /**
     * Greek
     */
    public const string GREK = 'Grek';

    /**
     * Gujarati
     */
    public const string GUJR = 'Gujr';

    /**
     * Gurung Khema
     */
    public const string GUKH = 'Gukh';

    /**
     * Gurmukhi
     */
    public const string GURU = 'Guru';

    /**
     * Han with Bopomofo (alias for Han + Bopomofo)
     */
    public const string HANB = 'Hanb';

    /**
     * Hangul (Hangŭl, Hangeul)
     */
    public const string HANG = 'Hang';

    /**
     * Han (Hanzi, Kanji, Hanja)
     */
    public const string HANI = 'Hani';

    /**
     * Hanunoo (Hanunóo)
     */
    public const string HANO = 'Hano';

    /**
     * Han (Simplified variant)
     */
    public const string HANS = 'Hans';

    /**
     * Han (Traditional variant)
     */
    public const string HANT = 'Hant';

    /**
     * Hatran
     */
    public const string HATR = 'Hatr';

    /**
     * Hebrew
     */
    public const string HEBR = 'Hebr';

    /**
     * Hiragana
     */
    public const string HIRA = 'Hira';

    /**
     * Anatolian Hieroglyphs (Luwian Hieroglyphs, Hittite Hieroglyphs)
     */
    public const string HLUW = 'Hluw';

    /**
     * Pahawh Hmong
     */
    public const string HMNG = 'Hmng';

    /**
     * Nyiakeng Puachue Hmong
     */
    public const string HMNP = 'Hmnp';

    /**
     * Han (Traditional variant) with Latin (alias for Hant + Latn)
     */
    public const string HNTL = 'Hntl';

    /**
     * Japanese syllabaries (alias for Hiragana + Katakana)
     */
    public const string HRKT = 'Hrkt';

    /**
     * Old Hungarian (Hungarian Runic)
     */
    public const string HUNG = 'Hung';

    /**
     * Indus (Harappan)
     */
    public const string INDS = 'Inds';

    /**
     * Old Italic (Etruscan, Oscan, etc.)
     */
    public const string ITAL = 'Ital';

    /**
     * Jamo (alias for Jamo subset of Hangul)
     */
    public const string JAMO = 'Jamo';

    /**
     * Javanese
     */
    public const string JAVA = 'Java';

    /**
     * Japanese (alias for Han + Hiragana + Katakana)
     */
    public const string JPAN = 'Jpan';

    /**
     * Jurchen
     */
    public const string JURC = 'Jurc';

    /**
     * Kayah Li
     */
    public const string KALI = 'Kali';

    /**
     * Katakana
     */
    public const string KANA = 'Kana';

    /**
     * Kawi
     */
    public const string KAWI = 'Kawi';

    /**
     * Kharoshthi
     */
    public const string KHAR = 'Khar';

    /**
     * Khmer
     */
    public const string KHMR = 'Khmr';

    /**
     * Khojki
     */
    public const string KHOJ = 'Khoj';

    /**
     * Khitan large script
     */
    public const string KITL = 'Kitl';

    /**
     * Khitan small script
     */
    public const string KITS = 'Kits';

    /**
     * Kannada
     */
    public const string KNDA = 'Knda';

    /**
     * Korean (alias for Hangul + Han)
     */
    public const string KORE = 'Kore';

    /**
     * Kpelle
     */
    public const string KPEL = 'Kpel';

    /**
     * Kirat Rai
     */
    public const string KRAI = 'Krai';

    /**
     * Kaithi
     */
    public const string KTHI = 'Kthi';

    /**
     * Tai Tham (Lanna)
     */
    public const string LANA = 'Lana';

    /**
     * Lao
     */
    public const string LAOO = 'Laoo';

    /**
     * Latin (Fraktur variant)
     */
    public const string LATF = 'Latf';

    /**
     * Latin (Gaelic variant)
     */
    public const string LATG = 'Latg';

    /**
     * Latin
     */
    public const string LATN = 'Latn';

    /**
     * Leke
     */
    public const string LEKE = 'Leke';

    /**
     * Lepcha (Róng)
     */
    public const string LEPC = 'Lepc';

    /**
     * Limbu
     */
    public const string LIMB = 'Limb';

    /**
     * Linear A
     */
    public const string LINA = 'Lina';

    /**
     * Linear B
     */
    public const string LINB = 'Linb';

    /**
     * Lisu (Fraser)
     */
    public const string LISU = 'Lisu';

    /**
     * Loma
     */
    public const string LOMA = 'Loma';

    /**
     * Lycian
     */
    public const string LYCI = 'Lyci';

    /**
     * Lydian
     */
    public const string LYDI = 'Lydi';

    /**
     * Mahajani
     */
    public const string MAHJ = 'Mahj';

    /**
     * Makasar
     */
    public const string MAKA = 'Maka';

    /**
     * Mandaic, Mandaean
     */
    public const string MAND = 'Mand';

    /**
     * Manichaean
     */
    public const string MANI = 'Mani';

    /**
     * Marchen
     */
    public const string MARC = 'Marc';

    /**
     * Mayan hieroglyphs
     */
    public const string MAYA = 'Maya';

    /**
     * Medefaidrin (Oberi Okaime, Oberi Ɔkaimɛ)
     */
    public const string MEDF = 'Medf';

    /**
     * Mende Kikakui
     */
    public const string MEND = 'Mend';

    /**
     * Meroitic Cursive
     */
    public const string MERC = 'Merc';

    /**
     * Meroitic Hieroglyphs
     */
    public const string MERO = 'Mero';

    /**
     * Malayalam
     */
    public const string MLYM = 'Mlym';

    /**
     * Modi, Moḍī
     */
    public const string MODI = 'Modi';

    /**
     * Mongolian
     */
    public const string MONG = 'Mong';

    /**
     * Moon (Moon code, Moon script, Moon type)
     */
    public const string MOON = 'Moon';

    /**
     * Mro, Mru
     */
    public const string MROO = 'Mroo';

    /**
     * Meitei Mayek (Meithei, Meetei)
     */
    public const string MTEI = 'Mtei';

    /**
     * Multani
     */
    public const string MULT = 'Mult';

    /**
     * Myanmar (Burmese)
     */
    public const string MYMR = 'Mymr';

    /**
     * Nag Mundari
     */
    public const string NAGM = 'Nagm';

    /**
     * Nandinagari
     */
    public const string NAND = 'Nand';

    /**
     * Old North Arabian (Ancient North Arabian)
     */
    public const string NARB = 'Narb';

    /**
     * Nabataean
     */
    public const string NBAT = 'Nbat';

    /**
     * Newa, Newar, Newari, Nepāla lipi
     */
    public const string NEWA = 'Newa';

    /**
     * Naxi Dongba (na²¹ɕi³³ to³³ba²¹, Nakhi Tomba)
     */
    public const string NKDB = 'Nkdb';

    /**
     * Naxi Geba (na²¹ɕi³³ gʌ²¹ba²¹, 'Na-'Khi ²Ggŏ-¹baw, Nakhi Geba)
     */
    public const string NKGB = 'Nkgb';

    /**
     * N'Ko
     */
    public const string NKOO = 'Nkoo';

    /**
     * Nüshu
     */
    public const string NSHU = 'Nshu';

    /**
     * Ogham
     */
    public const string OGAM = 'Ogam';

    /**
     * Ol Chiki (Ol Cemet', Ol, Santali)
     */
    public const string OLCK = 'Olck';

    /**
     * Ol Onal
     */
    public const string ONAO = 'Onao';

    /**
     * Old Turkic, Orkhon Runic
     */
    public const string ORKH = 'Orkh';

    /**
     * Oriya (Odia)
     */
    public const string ORYA = 'Orya';

    /**
     * Osage
     */
    public const string OSGE = 'Osge';

    /**
     * Osmanya
     */
    public const string OSMA = 'Osma';

    /**
     * Old Uyghur
     */
    public const string OUGR = 'Ougr';

    /**
     * Palmyrene
     */
    public const string PALM = 'Palm';

    /**
     * Pau Cin Hau
     */
    public const string PAUC = 'Pauc';

    /**
     * Proto-Cuneiform
     */
    public const string PCUN = 'Pcun';

    /**
     * Proto-Elamite
     */
    public const string PELM = 'Pelm';

    /**
     * Old Permic
     */
    public const string PERM = 'Perm';

    /**
     * Phags-pa
     */
    public const string PHAG = 'Phag';

    /**
     * Inscriptional Pahlavi
     */
    public const string PHLI = 'Phli';

    /**
     * Psalter Pahlavi
     */
    public const string PHLP = 'Phlp';

    /**
     * Book Pahlavi
     */
    public const string PHLV = 'Phlv';

    /**
     * Phoenician
     */
    public const string PHNX = 'Phnx';

    /**
     * Miao (Pollard)
     */
    public const string PLRD = 'Plrd';

    /**
     * Klingon (KLI pIqaD)
     */
    public const string PIQD = 'Piqd';

    /**
     * Inscriptional Parthian
     */
    public const string PRTI = 'Prti';

    /**
     * Proto-Sinaitic
     */
    public const string PSIN = 'Psin';

    /**
     * Reserved for private use (start)
     */
    public const string QAAA = 'Qaaa';

    /**
     * Reserved for private use (end)
     */
    public const string QABX = 'Qabx';

    /**
     * Ranjana
     */
    public const string RANJ = 'Ranj';

    /**
     * Rejang (Redjang, Kaganga)
     */
    public const string RJNG = 'Rjng';

    /**
     * Hanifi Rohingya
     */
    public const string ROHG = 'Rohg';

    /**
     * Rongorongo
     */
    public const string RORO = 'Roro';

    /**
     * Runic
     */
    public const string RUNR = 'Runr';

    /**
     * Samaritan
     */
    public const string SAMR = 'Samr';

    /**
     * Sarati
     */
    public const string SARA = 'Sara';

    /**
     * Old South Arabian
     */
    public const string SARB = 'Sarb';

    /**
     * Saurashtra
     */
    public const string SAUR = 'Saur';

    /**
     * (Small) Seal
     */
    public const string SEAL = 'Seal';

    /**
     * SignWriting
     */
    public const string SGNW = 'Sgnw';

    /**
     * Shavian (Shaw)
     */
    public const string SHAW = 'Shaw';

    /**
     * Sharada, Śāradā
     */
    public const string SHRD = 'Shrd';

    /**
     * Shuishu
     */
    public const string SHUI = 'Shui';

    /**
     * Siddham, Siddhaṃ, Siddhamātṛkā
     */
    public const string SIDD = 'Sidd';

    /**
     * Sidetic
     */
    public const string SIDT = 'Sidt';

    /**
     * Khudawadi, Sindhi
     */
    public const string SIND = 'Sind';

    /**
     * Sinhala
     */
    public const string SINH = 'Sinh';

    /**
     * Sogdian
     */
    public const string SOGD = 'Sogd';

    /**
     * Old Sogdian
     */
    public const string SOGO = 'Sogo';

    /**
     * Sora Sompeng
     */
    public const string SORA = 'Sora';

    /**
     * Soyombo
     */
    public const string SOYO = 'Soyo';

    /**
     * Sundanese
     */
    public const string SUND = 'Sund';

    /**
     * Sunuwar
     */
    public const string SUNU = 'Sunu';

    /**
     * Syloti Nagri
     */
    public const string SYLO = 'Sylo';

    /**
     * Syriac
     */
    public const string SYRC = 'Syrc';

    /**
     * Syriac (Estrangelo variant)
     */
    public const string SYRE = 'Syre';

    /**
     * Syriac (Western variant)
     */
    public const string SYRJ = 'Syrj';

    /**
     * Syriac (Eastern variant)
     */
    public const string SYRN = 'Syrn';

    /**
     * Tagbanwa
     */
    public const string TAGB = 'Tagb';

    /**
     * Takri, Ṭākrī, Ṭāṅkrī
     */
    public const string TAKR = 'Takr';

    /**
     * Tai Le
     */
    public const string TALE = 'Tale';

    /**
     * New Tai Lue
     */
    public const string TALU = 'Talu';

    /**
     * Tamil
     */
    public const string TAML = 'Taml';

    /**
     * Tangut
     */
    public const string TANG = 'Tang';

    /**
     * Tai Viet
     */
    public const string TAVT = 'Tavt';

    /**
     * Tai Yo
     */
    public const string TAYO = 'Tayo';

    /**
     * Telugu
     */
    public const string TELU = 'Telu';

    /**
     * Tengwar
     */
    public const string TENG = 'Teng';

    /**
     * Tifinagh (Berber)
     */
    public const string TFNG = 'Tfng';

    /**
     * Tagalog (Baybayin, Alibata)
     */
    public const string TGLG = 'Tglg';

    /**
     * Thaana
     */
    public const string THAA = 'Thaa';

    /**
     * Thai
     */
    public const string THAI = 'Thai';

    /**
     * Tibetan
     */
    public const string TIBT = 'Tibt';

    /**
     * Tirhuta
     */
    public const string TIRH = 'Tirh';

    /**
     * Tangsa
     */
    public const string TNSA = 'Tnsa';

    /**
     * Todhri
     */
    public const string TODR = 'Todr';

    /**
     * Tolong Siki
     */
    public const string TOLS = 'Tols';

    /**
     * Toto
     */
    public const string TOTO = 'Toto';

    /**
     * Tulu-Tigalari
     */
    public const string TUTG = 'Tutg';

    /**
     * Ugaritic
     */
    public const string UGAR = 'Ugar';

    /**
     * Vai
     */
    public const string VAII = 'Vaii';

    /**
     * Visible Speech
     */
    public const string VISP = 'Visp';

    /**
     * Vithkuqi
     */
    public const string VITH = 'Vith';

    /**
     * Warang Citi (Varang Kshiti)
     */
    public const string WARA = 'Wara';

    /**
     * Wancho
     */
    public const string WCHO = 'Wcho';

    /**
     * Woleai
     */
    public const string WOLE = 'Wole';

    /**
     * Old Persian
     */
    public const string XPEO = 'Xpeo';

    /**
     * Cuneiform, Sumero-Akkadian
     */
    public const string XSUX = 'Xsux';

    /**
     * Yezidi
     */
    public const string YEZI = 'Yezi';

    /**
     * Yi
     */
    public const string YIII = 'Yiii';

    /**
     * Zanabazar Square (Zanabazarin Dörböljin Useg, Xewtee Dörböljin Bicig, Horizontal Square Script)
     */
    public const string ZANB = 'Zanb';

    /**
     * Code for inherited script
     */
    public const string ZINH = 'Zinh';

    /**
     * Mathematical notation
     */
    public const string ZMTH = 'Zmth';

    /**
     * Symbols (Emoji variant)
     */
    public const string ZSYE = 'Zsye';

    /**
     * Symbols
     */
    public const string ZSYM = 'Zsym';

    /**
     * Code for unwritten documents
     */
    public const string ZXXX = 'Zxxx';

    /**
     * Code for undetermined script
     */
    public const string ZYYY = 'Zyyy';

    /**
     * Code for uncoded script
     */
    public const string ZZZZ = 'Zzzz';
}