<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserLogdbController extends AbstractController
{
    /**
     * @Route("/dashboard", name="user_logdb")
     */
    public function index()
    {
        return $this->render('user_logdb/index.html.twig', [
            'controller_name' => 'UserLogdbController',
        ]);
    }
}
