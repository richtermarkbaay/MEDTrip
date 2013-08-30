<?php
/**
 * Advertisement Media Service
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use Gaufrette\Filesystem;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;
use HealthCareAbroad\MediaBundle\Services\MediaService;

class AdvertisementMediaService extends MediaService
{
    const ICON_TYPE_IMAGE = 1;
    const HIGHLIGHT_IMAGE = 2;
    const MEDIUM_BANNER_IMAGE = 3;

    protected $imageSizes = array(
        self::ICON_TYPE_IMAGE => array(ImageSizes::MINI, ImageSizes::ADS_FEATURED_IMAGE),
        self::HIGHLIGHT_IMAGE => array(ImageSizes::MINI),
        self::MEDIUM_BANNER_IMAGE => array(ImageSizes::MEDIUM_BANNER)
    );

    function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function setUploadDirectory($directory)
    {
        $this->uploadDirectory = $directory;
    }

    function upload($file, Advertisement $advertisement, $imageType = self::ICON_TYPE_IMAGE)
    {
        $result = parent::uploadFile($file);

        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType($imageType);
            $this->resize($media, $sizes, false);

            return $media;
        }

        return null; 
    }
    
    function delete($media, $imageType)
    {
        $sizes = $this->getSizesByType($imageType);

        parent::deleteMediaAndFiles($media, $sizes);
    }
    
    private function getSizesByType($imageType)
    {
        return isset($this->imageSizes[$imageType]) ? $this->imageSizes[$imageType] : array();
    }
}