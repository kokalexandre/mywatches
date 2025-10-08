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

        return $this->render('coffre/list.html.twig', [
            'coffres' => $coffres,
        ]);
    }


    /**
     * Afficher la fiche d'un coffre (inventaire)
     * @param int $id
     */
    #[Route('/coffre/{id}', name: 'coffre_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(CoffreRepository $coffreRepository, int $id): Response
    {
        $coffre = $coffreRepository->find($id);

        if (!$coffre) {
            throw $this->createNotFoundException('Ce coffre n’existe pas.');
        }

        return $this->render('coffre/show.html.twig', [
            'coffre' => $coffre,
        ]);
    }
}

