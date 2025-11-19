<?php

namespace App\Controller;

use App\Entity\Montre;
use App\Entity\Member;
use App\Form\MontreType;
use App\Entity\Coffre;
use App\Repository\MontreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/montre')]
final class MontreController extends AbstractController
{
    #[Route('/', name: 'app_montre_index', methods: ['GET'])]
    public function index(MontreRepository $montreRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $montres = $montreRepository->findAll();
        } else {
            $user = $this->getUser();

            if ($user instanceof Member) {
                $montres = $montreRepository->findMemberMontres($user);
            } else {
                $montres = [];
            }
        }

        return $this->render('montre/index.html.twig', [
            'montres' => $montres,
        ]);
    }


    #[Route('/new/{id}', name: 'app_montre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Coffre $coffre): Response
    {
     
        $montre = new Montre();
        $montre->setCoffre($coffre);

        $form = $this->createForm(MontreType::class, $montre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $uploadsDir = $this->getParameter('montres_images_directory');

                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                $newFilename = uniqid('montre_', true) . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadsDir, $newFilename);
                $montre->setImageFilename($newFilename);
            }

            $entityManager->persist($montre);
            $entityManager->flush();

            return $this->redirectToRoute('coffre_show', [
                'id' => $montre->getCoffre()->getId()
            ], Response::HTTP_SEE_OTHER);
        }


        return $this->render('montre/new.html.twig', [
            'montre' => $montre,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_montre_show', methods: ['GET'])]
    public function show(Montre $montre): Response
    {
        return $this->render('montre/show.html.twig', [
            'montre' => $montre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_montre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Montre $montre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MontreType::class, $montre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $uploadsDir = $this->getParameter('montres_images_directory');

                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                $newFilename = uniqid('montre_', true) . '.' . $imageFile->guessExtension();
                $imageFile->move($uploadsDir, $newFilename);
                $montre->setImageFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('coffre_show', [
                'id' => $montre->getCoffre()->getId()
            ], Response::HTTP_SEE_OTHER);
        }


        return $this->render('montre/edit.html.twig', [
            'montre' => $montre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_montre_delete', methods: ['POST'])]
    public function delete(Request $request, Montre $montre, EntityManagerInterface $entityManager): Response
    {
        $coffreId = $montre->getCoffre() ? $montre->getCoffre()->getId() : null;

        if ($this->isCsrfTokenValid('delete'.$montre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($montre);
            $entityManager->flush();
        }

        if ($coffreId !== null) {
            return $this->redirectToRoute(
                'coffre_show',  
                ['id' => $coffreId],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->redirectToRoute('app_montre_index', [], Response::HTTP_SEE_OTHER);
    }
}
