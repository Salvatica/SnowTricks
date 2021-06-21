<?php


namespace App\Service;


use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class AppMailer
{
    public function __construct(
        private string $appMail,
        private MailerInterface $mailer,
        private Environment $twig
    )
    {
    }

    public function sendActivationMail(User $user)
    {
        $message = (new Email())
            ->from($this->appMail)
            ->to($user->getEmail())
            ->subject('Activation de votre compte')
            ->html(
                $this->twig->render(
                    'security/activation.html.twig', ['token' => $user->getActivationToken()]
                ),
                'text/html');

        $this->mailer->send($message);
    }


    public function sendForgottenMail(User $user){
        dump($user);

        $message = (new Email())
            ->from($this->appMail)
            ->to($user->getEmail())
            ->subject('réinitialisation de votre mot de passe')
            ->html(
                $this->twig->render(
                    'security/forgottenPasswordMail.html.twig', ['token' => $user->getResetToken()]
                ),
            'text/html');


        $this->mailer->send($message);

    }

}