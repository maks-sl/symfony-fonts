<?php

declare(strict_types=1);

namespace App\Controller\Panel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/panel", name="panel")
     * @return Response
     */
    public function panel(): Response
    {
        return $this->render('panel/home.html.twig');
    }
}