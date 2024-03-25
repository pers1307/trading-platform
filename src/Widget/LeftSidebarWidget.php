<?php

namespace App\Widget;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LeftSidebarWidget extends AbstractController
{
    public function __construct(
        //        private readonly AccauntService $accauntService
    )
    {
    }

    public function show(): Response
    {
        //        $accaunts = $this->accauntService->findAll();
        return $this->render('widgets/left-sidebar/show.html.twig', [
            //            'accaunts' => $accaunts,
        ]);
    }
}
