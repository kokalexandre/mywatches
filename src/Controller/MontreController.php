<?php

namespace App\Controller;

use App\Repository\MontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MontreController extends AbstractController
{
    /**
     * Affiche la fiche d'une montre (pas de liste globale ici)
     */
    #[Route('/montre/{id}', name: 'montre_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(MontreRepository $montreRepository, int $id): Response
    {
        $montre = $montreRepository->find($id);

        if (!$montre) {
            throw $this->createNotFoundException('Cette montre n’existe pas.');
        }

        return $this->render('montre/show.html.twig', [
            'montre' => $montre,
        ]);
    }
}


