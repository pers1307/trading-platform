<?php

namespace App\Controller\Trade;

use App\Entity\Trade;
use App\Exception\NotFoundTradeException;
use App\Service\Trade\RemoveTradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RemoveTradeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RemoveTradeService $removeTradeService,
    ) {
    }

    #[Route('/trades/{id<\d+>}/confirm/remove', name: 'app_trade_confirm_remove')]
    public function confirmRemove(int $id, Request $request): Response
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $trade = $tradeRepository->findCompletely($id);
        if (is_null($trade)) {
            throw new NotFoundHttpException('Трейда не существует');
        }
        $referer = $request->headers->get('referer');
        $request->getSession()->set('refererBeforeRemove', $referer);

        return $this->render('trades/remove/confirm.remove.html.twig', [
            'trade' => $trade,
            'referer' => $referer,
        ]);
    }

    #[Route('/trades/{id<\d+>}/remove', name: 'app_trade_remove', methods: ['POST'])]
    public function remove(int $id, Request $request): Response
    {
        try {
            $this->removeTradeService->removeById($id);
        } catch (NotFoundTradeException) {
            throw new NotFoundHttpException('Трейда не существует');
        }

        $redirectUrl = $this->generateUrl('app_trade_list');
        if ($request->getSession()->has('refererBeforeRemove')) {
            $redirectUrl = $request->getSession()->get('refererBeforeRemove');
            $request->getSession()->remove('refererBeforeRemove');
        }

        return new RedirectResponse($redirectUrl);
    }
}
