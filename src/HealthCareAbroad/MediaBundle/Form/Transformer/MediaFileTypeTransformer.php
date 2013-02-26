<?php 

/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\MediaBundle\Form\Transformer;

use HealthCareAbroad\MediaBundle\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\AdminBundle\Entity\Langauge;
use Doctrine\ORM\EntityManager;

class MediaFileTypeTransformer implements DataTransformerInterface
{
    public function transform($media)
    {
        return $media;
    }

    public function reverseTransform($media)
    {
        return null;
    }
}