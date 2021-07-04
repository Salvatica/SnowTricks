<?php


namespace App\Controller;

use App\Form\RegistrationType;
use App\Form\ResetPassType;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Service\AppMailer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use http\Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\MessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request, UserManager $userManager): RedirectResponse|Response
    {

        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->register($user, $form['plainPassword']->getData());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/activation/{activationToken}', name: 'activation')]
    public function activation(User $user, UserManager $manager)
    {
        $manager->activate($user);
        $this->addFlash('message', 'You have successfully activated your account');

        return $this->redirectToRoute('homepage');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {

    }

    #[Route('/forgotten', name: 'app_forgotten_password')]
    public function forgottenPass(Request $request, UserRepository $userRepo, UserManager $userManager): Response
    {
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // on cherche si un utilisateur a cet email
            $user = $userRepo->findOneByEmail($data['email']);
            //si l'utilisateur n'existe pas
            if (!$user) {
                $this->addFlash('danger', 'this email doesn\'t exist');
                return $this->redirectToRoute('app_login');
            }
            $userManager->forgotPass($user);

            $this->addFlash('message', 'a password reset email has been sent to you');
            return $this->redirectToRoute('app_login');
        }
        // envoie vers la page de demande d'email
        return $this->render('security/forgottenPasswordForm.html.twig', ['emailForm' => $form->createView()]);

    }

    #[Route('/reset-pass/{token}', name: 'app_reset_password')]
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // va chercher l'utilisateur avec le token fourni
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
        if (!$user) {
            $this->addFlash('danger', 'Unknown Token');
            return $this->redirectToRoute('app_login');
        }
        // verifie si le fomulaire est envoyé en méthode post et on supprime le token
        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            // chiffre le mdp
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Password changed successfully');

            return $this->redirectToRoute('app_login');

        } else {
            return $this->render('security/forgottenPasswordNewForm.html.twig', ['token' => $token]);
        }
    }
}