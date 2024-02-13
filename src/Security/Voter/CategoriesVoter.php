<?php

namespace App\Security\Voter;

use App\Entity\Categories;
use Symfony\Bundle\SecurityBundle\Security;

class CategoriesVoter extends Voter
{
    const EDIT = "PRODUCT_EDIT";
    const DELETE = "PRODUCT_DELETE";

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supports(string $attribute, $category): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if(!$category instanceof Categories) {
            return false;
        }

        return true;
    }

    public function voteOnAttribute(): bool
    {

    }
}