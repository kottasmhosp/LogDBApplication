<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUsername(
                $form->get('username')->getData()
            );

            $user->setAddress(
                $form->get('address')->getData()
            );
                        $user->setEmail(
                $form->get('email')->getData()
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully registered!!');
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_login');
        }
        $user = $entityManager->getRepository(User::class)->findBy(array('username' => $form->get('username')->getData()));
        if(!empty($user)) {
            $this->addFlash('error', 'Duplicate Username. Please pick another one');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
