<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Symfony\Component\Validator\Constraints\All;

final class PayingStatus
{
    const FREE_LISTING = 0;
    
    const LINKED_LISTING  = 1;
    
    const LOGO_LISTING = 2;
    
    const PHOTO_LISTING = 3;
    
    static public function all()
    {
        return array(
            self::FREE_LISTING => 'Free Listing',
            self::LINKED_LISTING => 'Linked Listing',
            self::LOGO_LISTING => 'Logo Listing',
            self::PHOTO_LISTING => 'Photo Listing',
        );
    }
}