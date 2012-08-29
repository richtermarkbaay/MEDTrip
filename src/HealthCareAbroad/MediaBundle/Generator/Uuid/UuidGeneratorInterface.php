<?php

namespace HealthCareAbroad\MediaBundle\Generator\Uuid;

use HealthCareAbroad\MediaBundle\Entity\Media;

interface UuidGeneratorInterface
{
    function generateUuid(Media $media);
}