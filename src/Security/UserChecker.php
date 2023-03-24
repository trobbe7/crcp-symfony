<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->isVerified()) {
            // Si l'adresse mail n'est pas vérifiée, la connexion est refusée et une Exception retournée
            throw new CustomUserMessageAccountStatusException("Votre adresse mail n'est pas vérifiée");
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        
    }
}