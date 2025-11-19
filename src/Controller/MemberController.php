<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/member')]
final class MemberController extends AbstractController
{
    #[Route(name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/me', name: 'app_member_me', methods: ['GET'])]
    public function me(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Member) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }


        return $this->redirectToRoute('app_member_show', [
            'id' => $user->getId(),
        ]);
    }
}
