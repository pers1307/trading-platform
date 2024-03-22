<?php

namespace App\Controller;

use App\Entity\Accaunt;
use App\Entity\AccauntHistory;
use App\Service\AccauntHistoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AccauntHistoryController extends AbstractController
{
    #[Route('/accaunt/{id<\d+>}/history', name: 'app_accaunt_history_list')]
    public function list(int $id, AccauntHistoryService $accauntHistoryService): Response
    {
        [$accauntHistoryItems, $accaunt, $graphDataEncode] = $accauntHistoryService->getDataByAccauntId($id);

        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        return $this->render('accaunt_history/list.html.twig', [
            'accauntHistoryItems' => $accauntHistoryItems,
            'accaunt' => $accaunt,
            'graphDataEncode' => $graphDataEncode,
        ]);
    }

    #[Route('/accaunt/{id<\d+>}/history/add', name: 'app_accaunt_history_add_form', methods: ['GET'])]
    public function add(int $id, EntityManagerInterface $entityManager): Response
    {
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        return $this->render('accaunt_history/add.html.twig', [
            'accaunt' => $accaunt,
        ]);
    }

    #[Route('/accaunt/{id<\d+>}/history/add', name: 'app_accaunt_history_add', methods: ['POST'])]
    public function addInBase(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $accauntRepository = $entityManager->getRepository(Accaunt::class);
        $accaunt = $accauntRepository->find($id);
        if (is_null($accaunt)) {
            throw new NotFoundHttpException();
        }

        $value = $request->get('value');

        $accauntHistory = new AccauntHistory();
        $accauntHistory->setAccaunt($accaunt);
        $accauntHistory->setBalance(floatval($value));

        $entityManager->persist($accauntHistory);
        $entityManager->flush();

        $listUrlByAccauntId = $this->generateUrl('app_accaunt_history_list', ['id' => $accaunt->getId()]);
        return new RedirectResponse($listUrlByAccauntId);
    }
}
