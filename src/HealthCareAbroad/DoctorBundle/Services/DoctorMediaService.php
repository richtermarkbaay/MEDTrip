<?php
/**
 * Doctor Media Service
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\DoctorBundle\Services;

use Gaufrette\Filesystem;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use HealthCareAbroad\MediaBundle\Services\ImageSizes;
use HealthCareAbroad\MediaBundle\Services\MediaService;

class DoctorMediaService extends MediaService
{
    const LOGO_TYPE_IMAGE = 1;
    
    public $mediaTwigExtension;
    
    private $imageSizes = array(
        self::LOGO_TYPE_IMAGE => array(ImageSizes::DOCTOR_LOGO) 
    );   

    function setFilesystem(Filesystem$filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function setUploadDirectory($directory)
    {
        $this->uploadDirectory = $directory;
    }

    
    function setMediaTwigExtension($mediaTwigExtension)
    {
        $this->mediaTwigExtension = $mediaTwigExtension;
    }

    function uploadLogo($file, Doctor $doctor, $flushObject = true)
    {
        $result = parent::uploadFile($file);

        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType(self::LOGO_TYPE_IMAGE);

            // Delete current logo
            $this->deleteMediaAndFiles($doctor->getMedia(), $sizes);

            // set newly uploaded logo
            $doctor->setMedia($media);

            $this->resize($media, $sizes, false);
            
            if($flushObject) {
                $this->entityManager->persist($doctor);
                $this->entityManager->flush($doctor);
            }

            return $media;
        }

        return null; 
    }

    function delete($media, $imageType = '')
    {
        $sizes = $this->getSizesByType($imageType);
    
        parent::deleteMediaAndFiles($media, $sizes);
    }
    
    public function getSizesByType($imageType)
    {
        return isset($this->imageSizes[$imageType]) ? $this->imageSizes[$imageType] : array();
    }
}