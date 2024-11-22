<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedController extends AbstractController
{
    #[Route('/med', name: 'app_med')]
    public function index(): Response
    {
        return $this->render('med/index.html.twig', [
            'controller_name' => 'MedController',
        ]);
    }
}
