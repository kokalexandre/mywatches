<?php

namespace App\DataFixtures;

use App\Entity\Coffre;
use App\Entity\Montre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Member;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Vitrine;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Génère des données d'initialisation pour les coffres : [description]
     * @return \Generator<array{0:string}>
     */
    private static function coffresDataGenerator(): \Generator
    {
        yield ["Coffre #1 - quotidien"];
        yield ["Coffre #2 - collection"];
        yield ["Coffre #3 - vintage"];
        yield ["Coffre #4 - sport"];
        yield ["Coffre #5 - dress"];
    }

    /**
     * Génère des données d'initialisation pour les montres :
     * [description, marque, reference, annee|null, coffre_description]
     * @return \Generator<array{0:string,1:string,2:string,3:?int,4:string}>
     */
    private static function montresDataGenerator(): \Generator
    {
        yield ["Plongeuse robuste",      "Seiko",               "SKX007",    2010, "Coffre #1 - quotidien"];
        yield ["Chronographe auto",      "Omega",               "Speedmaster",2005, "Coffre #1 - quotidien"];
        yield ["Dress watch",            "Tissot",              "Visodate",  2018, "Coffre #2 - collection"];
        yield ["Field watch",            "Hamilton",            "Khaki",     null, "Coffre #2 - collection"];
        yield ["Sport chic iconique",    "Audemars Piguet",     "Royal Oak 15202ST", 2012, "Coffre #4 - sport"];
        yield ["Sport chic iconique",    "Patek Philippe",      "Nautilus 5711/1A",  2015, "Coffre #4 - sport"];
        yield ["Sport chic contemporain","Vacheron Constantin", "Overseas 4500V",    2019, "Coffre #4 - sport"];
        yield ["Plongeuse moderne",      "Rolex",               "Submariner 124060", 2022, "Coffre #4 - sport"];
        yield ["Plongeuse néo-vintage",  "Tudor",               "Black Bay 58",      2020, "Coffre #4 - sport"];
        yield ["Plongeuse céramique",    "Omega",               "Seamaster 300M",    2019, "Coffre #4 - sport"];
        yield ["Finition haut de gamme", "Grand Seiko",         "SBGA211 \"Snowflake\"", 2021, "Coffre #1 - quotidien"];
        yield ["Classique habillée",     "Patek Philippe",      "Calatrava 6119R",   2021, "Coffre #5 - dress"];
        yield ["Icône art déco",         "Jaeger-LeCoultre",    "Reverso Classic",   2016, "Coffre #5 - dress"];
        yield ["Icône élégante",         "Cartier",             "Tank Louis",        2018, "Coffre #5 - dress"];
        yield ["Haute horlogerie",       "A. Lange & Söhne",    "Lange 1",           2010, "Coffre #5 - dress"];
        yield ["Classique guilloché",    "Breguet",             "Classique 5177",    2017, "Coffre #5 - dress"];
        yield ["Minimalisme bauhaus",    "Nomos Glashütte",     "Tangente 38",       2020, "Coffre #5 - dress"];
        yield ["Chronographe de pilote", "IWC",                 "Portugieser Chronograph", 2015, "Coffre #2 - collection"];
        yield ["Chronographe aviation",  "Breitling",           "Navitimer 01",      2014, "Coffre #2 - collection"];
        yield ["Toolwatch iconique",     "Panerai",             "Luminor Base PAM00112", 2008, "Coffre #3 - vintage"];
        yield ["Plongeuse vintage",      "Seiko",               "62MAS 6217-8001",   1967, "Coffre #3 - vintage"];
    }

    /**
    * Génère des vitrines de démonstration :
    *  [description, publiee(bool), montres: array<[marque, reference]>]
    * @return \Generator<array{0:string,1:bool,2:array<int,array{0:string,1:string}>}>
    */
    private static function vitrinesDataGenerator(): \Generator
    {
        yield ["Sport et Toolwatches", true, [
            ["Rolex", "Submariner 124060"],
            ["Omega", "Seamaster 300M"],
            ["Tudor", "Black Bay 58"],
            ["Seiko", "SKX007"],
            ["Hamilton", "Khaki"],
        ]];

        yield ["Dress watches", false, [
            ["Patek Philippe", "Calatrava 6119R"],
            ["Jaeger-LeCoultre", "Reverso Classic"],
            ["Cartier", "Tank Louis"],
            ["A. Lange & Söhne", "Lange 1"],
            ["Breguet", "Classique 5177"],
            ["Grand Seiko", "SBGA211 \"Snowflake\""],
            ["Nomos Glashütte", "Tangente 38"],
        ]];

        yield ["Vintage et Icônes", true, [
            ["Seiko", "62MAS 6217-8001"],
            ["Panerai", "Luminor Base PAM00112"],
            ["Omega", "Speedmaster"],
            ["Audemars Piguet", "Royal Oak 15202ST"],
            ["Patek Philippe", "Nautilus 5711/1A"],
            ["Vacheron Constantin", "Overseas 4500V"],
        ]];
    }


    /**
     * Generates initialization data for members :
     *  [email, plain text password]
     * @return \\Generator
     */
    private function membersGenerator()
    {
        yield ['olivier@localhost','123456'];
        yield ['slash@localhost','123456'];
    }

/**
 * Génère des données de test arborescentes par membre (version XL) :
 *
 * Structure de retour :
 * yield [
 *   'member'   => ['email' => string, 'password' => string],
 *   'coffre'   => ['description' => string],
 *   'montres'  => [
 *       ['description'=> string, 'marque'=> string, 'reference'=> string, 'annee'=> ?int],
 *       ...
 *   ],
 *   'vitrines' => [
 *       ['description'=> string, 'publiee'=> bool, 'montres'=> [ ['marque'=>string,'reference'=>string], ... ]],
 *       ...
 *   ],
 * ];
 *
 * @return \Generator
 */
private static function membersTreeGenerator(): \Generator
{
    // Membre 1 : quotidien & sport
    yield [
        'member'  => ['email' => 'test@localhost', 'password' => '123456'],
        'coffre'  => ['description' => 'Coffre #1 - quotidien & sport'],
        'montres' => [
            ['description' => 'Plongeuse robuste',           'marque' => 'Seiko',           'reference' => 'SKX007',               'annee' => 2010],
            ['description' => 'Chronographe de légende',     'marque' => 'Omega',           'reference' => 'Speedmaster Pro',      'annee' => 2004],
            ['description' => 'Toolwatch moderne',           'marque' => 'Rolex',           'reference' => 'Explorer I 214270',    'annee' => 2018],
            ['description' => 'GMT de voyage',               'marque' => 'Tudor',           'reference' => 'Black Bay GMT',        'annee' => 2021],
            ['description' => 'Diver céramique',             'marque' => 'Omega',           'reference' => 'Seamaster 300M 210.30','annee' => 2019],
        ],
        'vitrines' => [
            [
                'description' => 'Plongeuses & toolwatches',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Seiko', 'reference' => 'SKX007'],
                    ['marque' => 'Omega', 'reference' => 'Seamaster 300M 210.30'],
                    ['marque' => 'Rolex', 'reference' => 'Explorer I 214270'],
                    ['marque' => 'Tudor', 'reference' => 'Black Bay GMT'],
                ],
            ],
            [
                'description' => 'Speedmaster focus',
                'publiee'     => false,
                'montres'     => [
                    ['marque' => 'Omega', 'reference' => 'Speedmaster Pro'],
                ],
            ],
        ],
    ];

    // Membre 2 : dress & classiques
    yield [
        'member'  => ['email' => 'nicolas@localhost', 'password' => '123456'],
        'coffre'  => ['description' => 'Coffre #2 - dress & classiques'],
        'montres' => [
            ['description' => 'Dress simple',                'marque' => 'Tissot',              'reference' => 'Visodate',             'annee' => 2018],
            ['description' => 'Classique art déco',          'marque' => 'Jaeger-LeCoultre',    'reference' => 'Reverso Classic Medium','annee' => 2016],
            ['description' => 'Haute horlogerie iconique',   'marque' => 'A. Lange & Söhne',    'reference' => 'Saxonia Thin 37',      'annee' => 2019],
            ['description' => 'Classique guilloché',         'marque' => 'Breguet',             'reference' => 'Classique 5140',       'annee' => 2015],
            ['description' => 'Minimalisme bauhaus',         'marque' => 'Nomos Glashütte',     'reference' => 'Lambda 39',            'annee' => 2020],
        ],
        'vitrines' => [
            [
                'description' => 'Dress watches formelles',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Jaeger-LeCoultre', 'reference' => 'Reverso Classic Medium'],
                    ['marque' => 'A. Lange & Söhne', 'reference' => 'Saxonia Thin 37'],
                    ['marque' => 'Breguet',          'reference' => 'Classique 5140'],
                    ['marque' => 'Nomos Glashütte',  'reference' => 'Lambda 39'],
                ],
            ],
            [
                'description' => 'Dress accessibles',
                'publiee'     => false,
                'montres'     => [
                    ['marque' => 'Tissot', 'reference' => 'Visodate'],
                ],
            ],
        ],
    ];

    // Membre 3 : vintage & chrono
    yield [
        'member'  => ['email' => 'alexandre@localhost', 'password' => '123456'],
        'coffre'  => ['description' => 'Coffre #3 - vintage & chrono'],
        'montres' => [
            ['description' => 'Plongeuse 60s',               'marque' => 'Seiko',               'reference' => '62MAS 6217-8000',     'annee' => 1966],
            ['description' => 'Chronographe aviation',       'marque' => 'Breitling',           'reference' => 'Navitimer 806',       'annee' => 1972],
            ['description' => 'Chrono panda',                'marque' => 'Heuer',               'reference' => 'Autavia 2446C',       'annee' => 1970],
            ['description' => 'Plongeuse française',         'marque' => 'Yema',                'reference' => 'Superman 53.00.16',   'annee' => 1975],
            ['description' => 'Chrono course auto',          'marque' => 'Omega',               'reference' => 'Speedmaster Mk II',   'annee' => 1971],
        ],
        'vitrines' => [
            [
                'description' => 'Chronos vintage',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Breitling', 'reference' => 'Navitimer 806'],
                    ['marque' => 'Heuer',     'reference' => 'Autavia 2446C'],
                    ['marque' => 'Omega',     'reference' => 'Speedmaster Mk II'],
                ],
            ],
            [
                'description' => 'Plongeuses vintage',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Seiko', 'reference' => '62MAS 6217-8000'],
                    ['marque' => 'Yema',  'reference' => 'Superman 53.00.16'],
                ],
            ],
        ],
    ];

    // Membre 4 : indépendants & micro-marques
    yield [
        'member'  => ['email' => 'quentin@localhost', 'password' => '123456'],
        'coffre'  => ['description' => 'Coffre #4 - indépendants & micro-marques'],
        'montres' => [
            ['description' => 'Indépendant japonais',        'marque' => 'Kurono Tokyo',        'reference' => 'Chronograph 1',       'annee' => 2022],
            ['description' => 'Micro-marque française',      'marque' => 'Serica',              'reference' => '4512 Commando',       'annee' => 2021],
            ['description' => 'Plongeuse titanium',          'marque' => 'Baltic',              'reference' => 'Aquascaphe Titanium', 'annee' => 2022],
            ['description' => 'Field suisse moderne',        'marque' => 'Formex',              'reference' => 'Field Automatic',     'annee' => 2023],
            ['description' => 'Dress suisse indépendante',   'marque' => 'Habring²',            'reference' => 'Felix',               'annee' => 2019],
        ],
        'vitrines' => [
            [
                'description' => 'Micro-marques toolwatch',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Serica', 'reference' => '4512 Commando'],
                    ['marque' => 'Baltic', 'reference' => 'Aquascaphe Titanium'],
                    ['marque' => 'Formex', 'reference' => 'Field Automatic'],
                ],
            ],
            [
                'description' => 'Indépendants haut de gamme',
                'publiee'     => false,
                'montres'     => [
                    ['marque' => 'Kurono Tokyo', 'reference' => 'Chronograph 1'],
                    ['marque' => 'Habring²',     'reference' => 'Felix'],
                ],
            ],
        ],
    ];

    // Membre 5 : G-Shock & digitales / usage intensif
    yield [
        'member'  => ['email' => 'catherine@localhost', 'password' => '123456'],
        'coffre'  => ['description' => 'Coffre #5 - G-Shock & digitales'],
        'montres' => [
            ['description' => 'G-Shock carrée',              'marque' => 'Casio',               'reference' => 'GWM5610-1',           'annee' => 2018],
            ['description' => 'G-Shock métal',               'marque' => 'Casio',               'reference' => 'GMW-B5000D-1',        'annee' => 2020],
            ['description' => 'Digitale rétro',              'marque' => 'Casio',               'reference' => 'A168WA-1',            'annee' => 2015],
            ['description' => 'Solar tough',                 'marque' => 'Casio',               'reference' => 'PRW-3000 Pro Trek',   'annee' => 2017],
            ['description' => 'Smartwatch sportive',         'marque' => 'Garmin',              'reference' => 'Fenix 7X',            'annee' => 2023],
        ],
        'vitrines' => [
            [
                'description' => 'G-Shock favorites',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Casio', 'reference' => 'GWM5610-1'],
                    ['marque' => 'Casio', 'reference' => 'GMW-B5000D-1'],
                    ['marque' => 'Casio', 'reference' => 'A168WA-1'],
                ],
            ],
            [
                'description' => 'Outdoor & sport',
                'publiee'     => true,
                'montres'     => [
                    ['marque' => 'Casio',  'reference' => 'PRW-3000 Pro Trek'],
                    ['marque' => 'Garmin', 'reference' => 'Fenix 7X'],
                ],
            ],
        ],
    ];
}



public function load(ObjectManager $manager): void
{
    foreach (self::membersTreeGenerator() as $row) {
        // 1) Membre
        $user = new Member();
        $user->setEmail($row['member']['email']);
        $user->setPassword($this->hasher->hashPassword($user, $row['member']['password']));
        $manager->persist($user);

        // 2) Coffre (OneToOne avec Member)
        $coffre = new Coffre();
        $coffre->setDescription($row['coffre']['description'] ?? 'Coffre sans description');
        $coffre->setMember($user);
        $manager->persist($coffre);

        // 3) Montres (OneToMany Coffre -> Montre)
        $indexMontres = [];
        foreach ($row['montres'] as $m) {
            $montre = new Montre();
            $montre->setDescription($m['description'] ?? null);
            $montre->setMarque($m['marque'] ?? '');
            $montre->setReference($m['reference'] ?? '');
            $montre->setAnnee($m['annee'] ?? null);
            $montre->setCoffre($coffre);

            $manager->persist($montre);

            $key = mb_strtolower(trim(($m['marque'] ?? '').'|'.($m['reference'] ?? '')));
            $indexMontres[$key] = $montre;
        }

        // 4) Vitrines (ManyToMany Vitrine <-> Montre)
        foreach ($row['vitrines'] as $v) {
            $vitrine = new Vitrine();
            $vitrine->setDescription($v['description'] ?? '');
            $vitrine->setPubliee((bool)($v['publiee'] ?? false));

            $vitrine->setCreateur($user);

            foreach ($v['montres'] as $mref) {
                $k = mb_strtolower(trim(($mref['marque'] ?? '').'|'.($mref['reference'] ?? '')));
                if (isset($indexMontres[$k])) {
                    $vitrine->addMontre($indexMontres[$k]);
                }
            }
            
            $manager->persist($vitrine);
        }

    }
    $manager->flush();
}
}