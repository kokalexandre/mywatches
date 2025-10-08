<?php

namespace App\Controller;

use App\Repository\CoffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoffreController extends AbstractController
{

    #[Route('/', name: 'coffre_list', methods: ['GET'])]
    public function list(CoffreRepository $coffreRepository): Response
    {
        $coffres = $coffreRepository->findAll();

        $html = "<html><body>";
        $html .= "<h1>Liste des coffres (tous les membres)</h1>";

        if (count($coffres) === 0) {
            $html .= "<p>Aucun coffre en base.</p>";
        } else {
            $html .= "<ul>";
            foreach ($coffres as $coffre) {
            // 1) Générer l'URL de la fiche avec le nom de route 'coffre_show'
                $urlShow = $this->generateUrl('coffre_show', ['id' => $coffre->getId()]);

            // 2) Lier le texte à l'URL
            // (on n’affiche pas la description dans la liste, juste un lien vers la fiche)
                $html .= '<li><a href="'.$urlShow.'">Coffre #'.$coffre->getId().'</a></li>';
            }
            $html .= "</ul>";
        }

        $html .= "</body></html>";

        return new Response($html);
    }


    /**
     * Afficher la fiche d'un coffre (inventaire)
     * @param int $id
     */
    #[Route('/coffre/{id}', name: 'coffre_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(CoffreRepository $coffreRepository, int $id): Response
    {
        // 1) Charger l'entité
        $coffre = $coffreRepository->find($id);

        // 2) 404 si introuvable
        if (!$coffre) {
            throw $this->createNotFoundException('Ce coffre n’existe pas.');
        }

        // 3) Construire un HTML très simple
        $desc = htmlspecialchars((string)($coffre->getDescription() ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $backUrl = $this->generateUrl('coffre_list');

        $html  = "<html><body>";
        $html .= "<h1>Fiche Coffre #".$coffre->getId()."</h1>";
        $html .= "<p><strong>Description :</strong> {$desc}</p>";
        // (plus tard on pourra lister les montres du coffre ici)
        $html .= "<p><a href=\"{$backUrl}\">Retour à la liste</a></p>";
        $html .= "</body></html>";

        return new Response($html);
    }
}

