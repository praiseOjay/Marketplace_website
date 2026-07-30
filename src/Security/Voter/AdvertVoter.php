<?php

namespace App\Security\Voter;

use App\Entity\Advert;
use App\Entity\User;
use App\Enum\AdvertStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdvertVoter extends Voter
{
    public const EDIT = 'ADVERT_EDIT';
    public const DELETE = 'ADVERT_DELETE';
    public const SHOW = 'ADVERT_SHOW';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::SHOW])
            && $subject instanceof Advert;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /** @var Advert $advert */
        $advert = $subject;

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }

        return match ($attribute) {
            self::SHOW => $this->canShow($advert, $user),
            self::EDIT => $this->canEdit($advert, $user),
            self::DELETE => $this->canDelete($advert, $user),
            default => false,
        };
    }

    private function canShow(Advert $advert, mixed $user): bool
    {
        if ($advert->getIsPublished() || $advert->getStatus() === AdvertStatus::PUBLISHED) {
            return true;
        }

        if (!$user instanceof User) {
            return false;
        }

        return $advert->getUser() === $user;
    }

    private function canEdit(Advert $advert, mixed $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $advert->getUser() === $user;
    }

    private function canDelete(Advert $advert, mixed $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $advert->getUser() === $user;
    }
}
