<?php

namespace App\Widget;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class LeftSidebarWidget extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function show(): Response
    {
        $mainRequest = $this->requestStack->getMainRequest();
        $currentRoute = $mainRequest?->attributes->get('_route');

        return $this->render('widgets/left-sidebar/show.html.twig', [
            'currentRoute' => $currentRoute,
        ]);
    }
}
