<?php
namespace HealthCareAbroad\MediaBundle\Generator\Uuid;

use HealthCareAbroad\MediaBundle\Entity\Media;

class DefaultUuidGenerator implements UuidGeneratorInterface
{
    public function generateUuid(Media $media)
    {
        return uniqid();
    }
}