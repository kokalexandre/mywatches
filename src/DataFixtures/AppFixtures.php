<?php

namespace App\DataFixtures;

use App\Entity\Coffre;
use App\Entity\Montre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
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

        // Classiques — sport/chic
        yield ["Sport chic iconique",    "Audemars Piguet",     "Royal Oak 15202ST", 2012, "Coffre #4 - sport"];
        yield ["Sport chic iconique",    "Patek Philippe",      "Nautilus 5711/1A",  2015, "Coffre #4 - sport"];
        yield ["Sport chic contemporain","Vacheron Constantin", "Overseas 4500V",    2019, "Coffre #4 - sport"];
        yield ["Plongeuse moderne",      "Rolex",               "Submariner 124060", 2022, "Coffre #4 - sport"];
        yield ["Plongeuse néo-vintage",  "Tudor",               "Black Bay 58",      2020, "Coffre #4 - sport"];
        yield ["Plongeuse céramique",    "Omega",               "Seamaster 300M",    2019, "Coffre #4 - sport"];
        yield ["Finition haut de gamme", "Grand Seiko",         "SBGA211 \"Snowflake\"", 2021, "Coffre #1 - quotidien"];

        // Classiques — dress / habillées
        yield ["Classique habillée",     "Patek Philippe",      "Calatrava 6119R",   2021, "Coffre #5 - dress"];
        yield ["Icône art déco",         "Jaeger-LeCoultre",    "Reverso Classic",   2016, "Coffre #5 - dress"];
        yield ["Icône élégante",         "Cartier",             "Tank Louis",        2018, "Coffre #5 - dress"];
        yield ["Haute horlogerie",       "A. Lange & Söhne",    "Lange 1",           2010, "Coffre #5 - dress"];
        yield ["Classique guilloché",    "Breguet",             "Classique 5177",    2017, "Coffre #5 - dress"];
        yield ["Minimalisme bauhaus",    "Nomos Glashütte",     "Tangente 38",       2020, "Coffre #5 - dress"];

        // Chronographes
        yield ["Chronographe de pilote", "IWC",                 "Portugieser Chronograph", 2015, "Coffre #2 - collection"];
        yield ["Chronographe aviation",  "Breitling",           "Navitimer 01",      2014, "Coffre #2 - collection"];

        // Plongeuses / toolwatches additionnelles
        yield ["Toolwatch iconique",     "Panerai",             "Luminor Base PAM00112", 2008, "Coffre #3 - vintage"];
        yield ["Plongeuse vintage",      "Seiko",               "62MAS 6217-8001",   1967, "Coffre #3 - vintage"];
    }

    public function load(ObjectManager $manager): void
    {
        // 1) Créer et persister les Coffres
        foreach (self::coffresDataGenerator() as [$description]) {
            $coffre = (new Coffre())->setDescription($description);
            $manager->persist($coffre);
        }
        // On flush d’abord pour garantir des IDs et permettre les findOneBy sur description
        $manager->flush();

        // 2) Créer et persister les Montres liées aux Coffres
        $coffreRepo = $manager->getRepository(Coffre::class);
        foreach (self::montresDataGenerator() as [$desc, $marque, $ref, $annee, $coffreDesc]) {
            /** @var Coffre|null $coffre */
            $coffre = $coffreRepo->findOneBy(['description' => $coffreDesc]);

            if (!$coffre) {
                // Sécurité : si un coffre mentionné n’existe pas (typo), on le crée à la volée
                $coffre = (new Coffre())->setDescription($coffreDesc);
                $manager->persist($coffre);
            }

            $montre = (new Montre())
                ->setDescription($desc)
                ->setMarque($marque)
                ->setReference($ref)
                ->setAnnee($annee)
                ->setCoffre($coffre);

            $manager->persist($montre);
        }

        $manager->flush();
    }
}
