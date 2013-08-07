<?php
/**
 * Media Twig Extention Helper
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\MediaBundle\Twig\Extension;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

class MediaExtension extends \Twig_Extension
{
    protected $filesystemMapper;
    protected $mediaContext;

    function setFilesystemMapper(FilesystemMap $filesystemMapper)
    {
        $this->filesystemMapper = $filesystemMapper;
    }

    function setMediaContext($mediaContext)
    {
        $this->mediaContext = $mediaContext;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'media_src' => new \Twig_Function_Method($this, 'getMediaSrc'),
            'doctor_media_src' => new \Twig_Function_Method($this, 'getDoctorMediaSrc'),
            'institution_media_src' => new \Twig_Function_Method($this, 'getInstitutionMediaSrc'),
            'advertisement_media_src' => new \Twig_Function_Method($this, 'getAdvertisementMediaSrc'),
            'specialization_media_src' => new \Twig_Function_Method($this, 'getSpecializationMediaSrc'),
        );
    }

    /**
     * @param string|array|object $media
     * @param string $filesystemName
     * @param string $size
     * @return string Media source url.
     */
    public function getMediaSrc($media, $filesystemName, $size = '')
    {
        if(is_array($media) && isset($media['name'])) {
            $filename = $media['name'];
        } else if(is_object($media)) {
            $filename = $media->getName();
        } else {
            $filename = $media;
        }

        if($size) {
            $filenameWithSize = $size . '_' . $filename;
            if($this->filesystemMapper->get($filesystemName)->getAdapter()->exists($filenameWithSize)) {
                $filename = $filenameWithSize;
            }
        }

        return $this->filesystemMapper->get($filesystemName)->getAdapter()->getUrl() . '/' . $filename;
    }

    public function getDoctorMediaSrc($media, $size = '')
    {
        return $this->getMediaSrc($media, $this->mediaContext['doctor'], $size);
    }

    public function getInstitutionMediaSrc($media, $size = '')
    {
        return $this->getMediaSrc($media, $this->mediaContext['institution'], $size);
    }
    
    public function getAdvertisementMediaSrc($media, $size = '')
    {
        return $this->getMediaSrc($media, $this->mediaContext['advertisement'], $size);
    }
    
    public function getSpecializationMediaSrc($media, $size = '')
    {
        return $this->getMediaSrc($media, $this->mediaContext['specialization'], $size);
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