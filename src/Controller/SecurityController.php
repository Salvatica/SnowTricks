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
    private $userManager;
    private $appMailer;
    public function __construct(UserManager $userManager, AppMailer $appMailer)
    {
        $this->userManager = $userManager;
        $this->appMailer = $appMailer;
    }

    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request, UserManager $userManager): RedirectResponse|Response
    {

        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->register($user,$form['plainPassword']->getData());

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
        $this->addFlash('message', 'Vous avez bien activé votre compte');

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
    public function forgottenPass(Request $request, UserRepository $userRepo): Response
    {
        // on créé le formulaire

        $form = $this->createForm(ResetPassType::class);

        // on traite le formulaire
        $form->handleRequest($request);

        // si le formulaire est valide
        if($form->isSubmitted() && $form->isValid()){
            // on réécupère les donnees
            $data = $form->getData();
            // on cherche si un utilisateur a cet email
            $user = $userRepo->findOneByEmail($data['email']);
            //si l'utilisateur n'existe pas
            if(!$user){
                $this->addFlash('danger', 'cette adresse n\' existe pas');
                return $this->redirectToRoute('app_login');
            }
            $url = $this->userManager->forgotPass($user);
            $this->appMailer->sendForgottenMail($user);


           // on créé le message flash
            $this->addFlash('message', 'un Email de réinitialisation de mot de passe vous a été envoyé');
            return $this->redirectToRoute('app_login');
        }
        // on envoie vers la page de demande d'email
        return $this->render('security/forgottenPasswordForm.html.twig', ['emailForm' => $form->createView()]);

    }
    #[Route('/reset-pass/{token}', name: 'app_reset_password')]
        public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
        // on va chercher l'utilisateur avec le token fourni
        {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=> $token]);
        if(!$user){
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }
        // on verifie si le fomulaire est envoyé en méthode post
            if($request->isMethod('POST')){
                // on supprime le token
                $user->setResetToken(null);

                // on chiffre le mdp
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('message', 'Mot de passe modifié avec succès');

                return $this->redirectToRoute('app_login');

            }

            else{
                return $this->render('security/forgottenPasswordNewForm.html.twig', ['token'=>$token]);
            }
        }
}