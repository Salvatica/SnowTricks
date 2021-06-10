<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    const COMMENT_DELETE = 'comment_delete';

    protected function supports($attribute, $comment)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::COMMENT_DELETE])
            && $comment instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
// on vérifie si le commentaire à un propriétaire
        if (null === $comment->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::COMMENT_DELETE: // on vérifie si on peut supprimer
                return $this->canDelete($comment, $user);
                break;
        }

        return false;
    }

    private function canDelete(Comment $comment, User $user)
    {
        //le propriétaire du commentaire peut le supprimer
        return $comment->getUser() === $user;
    }
}
