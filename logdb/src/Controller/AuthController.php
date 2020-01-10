<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{


    /**
     * @Route("/api/register_jwt", name="app_register_jwt")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param DocumentManager $documentManager
     * @return Response
     * @throws MongoDBException
     */
    public function register_jwt(Request $request, UserPasswordEncoderInterface $encoder, DocumentManager $documentManager)
    {

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        //TODO search if user with username exists
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $documentManager->persist($user);
        $documentManager->flush();


        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }

    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()
            ->getUsername()));
    }
}