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
        $email = $request->request->get('_email');
        $phoneNumber = $request->request->get('_phonenumber');
        $address = $request->request->get('_address');

        $userExists = $documentManager->createQueryBuilder(User::class)
            ->field("username")
            ->equals($username)
            ->count()
            ->getQuery()
            ->execute();

        if($userExists != 0){
            return new Response(sprintf('User with username %s exists', $username));
        }


        //TODO search if user with username exists
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setPhoneNumber($phoneNumber);
        $user->setEmail($email);
        $user->setAddress($address);
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