<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CAN_EDIT', 'CAN_TOGGLE', 'CAN_DELETE'])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'CAN_EDIT':
                return $subject->getAuthor() === $user || ('anonyme' === $subject->getAuthor()->getUsername() && 'ROLE_ADMIN' === $user->getRoles()['0']);
            case 'CAN_TOGGLE':
                return $subject->getAuthor() === $user || ('anonyme' === $subject->getAuthor()->getUsername() && 'ROLE_ADMIN' === $user->getRoles()['0']);
            case 'CAN_DELETE':
                return $subject->getAuthor() === $user || ('anonyme' === $subject->getAuthor()->getUsername() && 'ROLE_ADMIN' === $user->getRoles()['0']);
        }

        return false;
    }
}
