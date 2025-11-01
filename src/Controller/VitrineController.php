<?php

namespace App\Controller;

use App\Entity\Vitrine;
use App\Entity\Montre;
use App\Form\VitrineType;
use App\Repository\VitrineRepository;
use App\Repository\MontreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vitrine')]
final class VitrineController extends AbstractController
{
    #[Route(name: 'app_vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $vitrineRepository): Response
    {
        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vitrine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vitrine = new Vitrine();
        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vitrine);
            $entityManager->flush();

            return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/new.html.twig', [
            'vitrine' => $vitrine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vitrine_show', methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        return $this->render('vitrine/show.html.twig', [
            'vitrine' => $vitrine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vitrine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vitrine $vitrine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/edit.html.twig', [
            'vitrine' => $vitrine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vitrine_delete', methods: ['POST'])]
    public function delete(Request $request, Vitrine $vitrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vitrine->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vitrine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vitrine_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche une Montre dans le contexte d'une Vitrine (publique).
     * URL finale (grâce au préfixe de classe /vitrine) :
     *   /vitrine/{vitrine_id}/montre/{montre_id}
     */
    #[Route(
        '/{vitrine_id}/montre/{montre_id}',
        name: 'app_vitrine_montre_show',
        requirements: ['vitrine_id' => '\d+', 'montre_id' => '\d+'],
        methods: ['GET']
    )]
    public function montreShow(
        #[MapEntity(id: 'vitrine_id')] Vitrine $vitrine,
        #[MapEntity(id: 'montre_id')]  Montre  $montre
    ): Response {
        if (!$vitrine->getMontres()->contains($montre)) {
            throw $this->createNotFoundException('Cette montre ne fait pas partie de cette vitrine.');
        }

        if (method_exists($vitrine, 'isPubliee') && !$vitrine->isPubliee()) {
            throw $this->createAccessDeniedException('Accès refusé : vitrine non publique.');
        }

        return $this->render('vitrine/montre_show.html.twig', [
            'vitrine' => $vitrine,
            'montre'  => $montre,
        ]);
    }
}

