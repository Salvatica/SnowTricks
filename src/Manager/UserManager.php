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
        $this->saveUser($user);
    }

    public function register(User $user, $plainPassword)
    {

        $hash = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($hash);

        // on génère le token d'activation
        $user->setActivationToken(md5(uniqid()));

        $this->saveUser($user);
        // on crée le message
        $this->appMailer->sendActivationMail($user);

    }

    /**
     * @param User $user
     * @return string
     */
    public function forgotPass(User $user)
    {
        $token = $this->createResetToken($user);
        $this->appMailer->sendForgottenMail($user);
    }

    /**
     * @param User $user
     * @param string $newPass
     */
    public function resetPass(User $user, string $newPass)
    {
        // on supprime le token temporaire
        $user->setResetToken(null);
        // chiffre le mdp
        $user->setPassword($this->passwordEncoder->encodePassword($user, $newPass));
        $this->saveUser($user);
    }

    /**
     * @param $token
     * @return User|null
     */
    public function loadUserByResetToken($token){
        return $this->entityManager->getRepository(User::class)->findOneBy(['reset_token' => $token]);
    }

    /**
     * @param User $user
     * @return string
     */
    private function createResetToken(User $user){
        $token = md5(uniqid());

        $user->setResetToken($token);
        $this->saveUser($user);
        return $token;
    }

    /**
     * @param User $user
     */
    private function saveUser(User $user){
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}