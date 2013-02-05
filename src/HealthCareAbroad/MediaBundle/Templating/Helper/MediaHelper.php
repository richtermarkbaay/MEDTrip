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

    public function getMedia(Media $media, $mediaOwner, $format = null, array $options = array())
    {

        $filesystem = $this->filesystemManager->get($mediaOwner);

        $options = $this->processOptions($media, $options);

        return $this->container->get('templating')->render($this->getTemplate($format ? $format : 'gallery'), array(
            'media' => $media,
            'format' => $format,
            'options' => $options,
            'src' => $this->filesystemManager->getWebPath() . '/' . $media->getName(),
            'src_thumb' => $this->filesystemManager->getWebPath() . '/' . $media->getName()
        ));
    }

    private function processOptions(Media $media, $options = array())
    {
        //$useIFrame = strpos($media->getContentType(), 'video/') !== false ? true : false;
        $useIFrame = is_numeric(strpos($media->getContentType(), 'video/'));

        $options['fancybox'] = array('useIFrame' => $useIFrame);

        return $options;
    }

    private function getTemplate($format)
    {
        $format = $format ? $format : 'default';
        return 'MediaBundle:Helper:'.$format.'Helper.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    function getName()
    {
        return 'media';
    }

}