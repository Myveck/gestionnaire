<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventsVoter extends Voter
{
    public const EDIT = 'EVENT_EDIT';
    public const DELETE = 'EVENT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $event): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $event instanceof \App\Entity\Events;
    }

    protected function voteOnAttribute(string $attribute, $event, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if the user is an admin
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->hasPermission();
                break;

            case self::DELETE:
                // logic to determine if the user can DELETE
                // return true or false
                return $this->hasPermission();
                break;
        }

        return false;
    }

    public function hasPermission() {
        return $this->security->isGranted('ROLE_GESTION_ADMIN');
    }
}
