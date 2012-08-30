<?php
namespace HealthCareAbroad\MediaBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\MediaBundle\Entity\Media;
use HealthCareAbroad\MediaBundle\Gaufrette\FilesystemManager;

class MediaHelper extends Helper
{
    protected $container;
	protected $filesystemManager;
    
    public function __construct(ContainerInterface $container, FilesystemManager $filesystemManager)
    {
        $this->container = $container;
        $this->filesystemManager = $filesystemManager;
    }

    public function getMedia(Media $media, $format = null, array $options = array())
    {
    	$institutionId = $media->getGallery()->first()->getInstitution()->getId();
    	
    	$filesystem = $this->filesystemManager->get($institutionId);
    	
        return $this->container->get('templating')->render('MediaBundle:Helper:mediaHelper.html.twig', array(
            'media' => $media,
            'format' => $format,
            'options' => $options,
        	'src' => $this->filesystemManager->getWebRootPath().$institutionId.'/'.$media->getName()
        ));
    }

    /**
     * {@inheritDoc}
     */
    function getName()
    {
        return 'media';
    }

}