<?php
namespace HealthCareAbroad\MediaBundle\Generator\Path;

use HealthCareAbroad\MediaBundle\Entity\Media;

interface PathGeneratorInterface
{
    function generatePath($basePath, $format);
}