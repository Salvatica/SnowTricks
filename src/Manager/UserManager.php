<?php


namespace App\Manager;


use App\Entity\User;
use App\Service\AppMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{


    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $passwordEncoder,
        private AppMailer $appMailer,
        private UrlGeneratorInterface $router)
    {

    }

    public function activate(User $user)
    {
        $user->setActivationToken(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function register(User $user, $plainPassword)
    {

        $hash = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($hash);

        // on génère le token d'activation
        $user->setActivationToken(md5(uniqid()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        // on crée le message
        $this->appMailer->sendActivationMail($user);

    }


    public function forgotPass(User $user)
    {
        $token = md5(uniqid());

        $user->setResetToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $url = $this->router->generate('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->appMailer->sendForgottenMail($user);
        return $url;

    }

    public function resetPass(User $user)
    {

    }
}