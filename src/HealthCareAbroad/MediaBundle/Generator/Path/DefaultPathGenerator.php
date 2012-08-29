<?php
namespace HealthCareAbroad\MediaBundle\Generator\Path;

use Gaufrette\Util\Path;

use HealthCareAbroad\MediaBundle\Entity\Media;
use HealthCareAbroad\MediaBundle\Util\File\ExtensionGuesser;

class DefaultPathGenerator implements PathGeneratorInterface
{
    public function generatePath($basePath, $discriminator)
    {
    	return Path::normalize($basePath.'/'. $discriminator);
    	
    	/*
        if (empty($discriminator)) {
            return sprintf(
                '%s/%s.%s',
                $media->getContext(),
                $media->getUuid(),
                ExtensionGuesser::guess($media->getContentType())
            );
        }

        return sprintf(
            '%s/%s_%s.%s',
            $media->getContext(),
            $media->getUuid(),
            $format,
            ExtensionGuesser::guess($media->getContentType())
        );
        */
    	
    	
    }
}