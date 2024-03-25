<?php

namespace App\Widget;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NotificationWidget extends AbstractController
{
    public function __construct(
        //        private readonly AccauntService $accauntService
    )
    {
    }

    public function show(): Response
    {
        //        $accaunts = $this->accauntService->findAll();
        return $this->render('widgets/notification/show.html.twig', [
            //            'accaunts' => $accaunts,
        ]);
    }
}
