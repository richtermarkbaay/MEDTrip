<?php
namespace HealthCareAbroad\MediaBundle\Twig\Extension;

use HealthCareAbroad\MediaBundle\Templating\Helper\MediaHelper;
use HealthCareAbroad\MediaBundle\Gaufrette\FilesystemManager;
use HealthCareAbroad\MediaBundle\Entity\Media;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var MediaHelper
     */
    protected $helper;

    public function __construct(MediaHelper $helper)
    {
        $this->helper = $helper;
        
//         $escaper = new \Twig_Extension_Escaper();
//         $twig = new \Twig_Environment();
//         $twig->addExtension($escaper);        
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'media' => new \Twig_Function_Method($this, 'getMedia'),
        );
    }

    public function getMedia(Media $media, $format = null, array $options = array())
    {
        return $this->helper->getMedia($media, $format, $options);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'media';
    }
}
