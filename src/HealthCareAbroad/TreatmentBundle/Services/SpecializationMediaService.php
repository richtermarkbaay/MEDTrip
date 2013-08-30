<?php
/**
 * Doctor Media Service
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\TreatmentBundle\Services;

use Gaufrette\Adapter\realpath;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Gaufrette\Filesystem;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use HealthCareAbroad\MediaBundle\Services\ImageSizes;
use HealthCareAbroad\MediaBundle\Services\MediaService;

class SpecializationMediaService extends MediaService
{
    const LOGO_TYPE_IMAGE = 1;
    
    protected $imageSizes = array(
        self::LOGO_TYPE_IMAGE => array(ImageSizes::SPECIALIZATION_DEFAULT_LOGO)
    );

    function setFilesystem(Filesystem $filesystem)
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
        $this->uploadDirectory = realpath($directory);
    }

    function uploadLogo($file, Specialization $specialization, $flushObject = true)
    {
        $result = parent::uploadFile($file);

        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType(self::LOGO_TYPE_IMAGE);

            // Delete current logo
            $this->deleteMediaAndFiles($specialization->getMedia(), $sizes);

            // set newly uploaded logo
            $specialization->setMedia($media);

            $this->resize($media, $sizes, false);
            
            if($flushObject) {
                $this->entityManager->persist($specialization);
                $this->entityManager->flush($specialization);
            }

            return $media;
        }

        return null; 
    }

    function getSizesByType($imageType)
    {       
        return isset($this->imageSizes[$imageType]) ? $this->imageSizes[$imageType] : array();  
    }
    
    function delete($media, $imageType)
    {
        $sizes = $this->getSizesByType($imageType);

        parent::deleteMediaAndFiles($media, $sizes);
    }
}