<?php
/**
 * Institution Media Service
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\MediaBundle\Services\FilesystemDirectory;

use Gaufrette\Filesystem;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MediaBundle\Entity\Gallery;
use HealthCareAbroad\MediaBundle\Services\ImageSizes;
use HealthCareAbroad\MediaBundle\Services\MediaService;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;


class InstitutionMediaService extends MediaService
{
    const LOGO_TYPE_IMAGE = 1;
    const FEATURED_TYPE_IMAGE = 2;
    const GALLERY_TYPE_IMAGE = 3;

    protected $imageSizes = array(
        self::LOGO_TYPE_IMAGE => array(ImageSizes::MINI, ImageSizes::SMALL, ImageSizes::MEDIUM),
        self::FEATURED_TYPE_IMAGE => array(ImageSizes::SMALL, ImageSizes::LARGE_BANNER),
        self::GALLERY_TYPE_IMAGE => array(ImageSizes::MINI, ImageSizes::MEDIUM, ImageSizes::GALLERY_LARGE_THUMBNAIL, ImageSizes::LARGE_BANNER)
    );
    
    protected $filesystemDirectory;

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
        $this->uploadDirectory = $directory;
    }
    
    public function uploadLogo($file, Institution $institution, $flushObject = true)
    {
        $result = parent::uploadFile($file);
        
        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType(self::LOGO_TYPE_IMAGE);

            // Delete current logo
            $this->deleteMediaAndFiles($institution->getLogo(), $sizes);
            
            // set newly uploaded logo
            $institution->setLogo($media);

            $this->resize($media, $sizes, false);

            if($flushObject) {
                $this->entityManager->persist($institution);
                $this->entityManager->flush($institution);                
            }

            return $media;
        }

        return null;
    }
    
    public function uploadFeaturedImage($file, Institution $institution, $flushObject = true)
    {
        $result = parent::uploadFile($file);
        
        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType(self::FEATURED_TYPE_IMAGE);

            // Delete current featured image
            $this->deleteMediaAndFiles($institution->getFeaturedMedia(), $sizes);

            // set newly uploaded featured image
            $institution->setFeaturedMedia($media);

            $this->resize($media, $sizes);
            
            if($flushObject) {
                $this->entityManager->persist($institution);
                $this->entityManager->flush($institution);
                
            }

            return $media;
        }

        return null;
    }
    
    public function uploadToGallery($file, Institution $institution, $flushObject = true)
    {
        $result = parent::uploadFile($file);

        if(is_object($result)) {
            $media = $result;
            $sizes = $this->getSizesByType(self::GALLERY_TYPE_IMAGE);
        
            $gallery = $this->entityManager->getRepository('MediaBundle:Gallery')->findOneByInstitution($institution->getId());
    
            if(!$gallery) {
                $gallery = new Gallery();
                $gallery->addMedia($media);
                $gallery->setInstitution($institution);
            } else {
                $gallery->addMedia($media);
            }

            $this->resize($media, $sizes);
            
            if($flushObject) {
                $this->entityManager->persist($gallery);
                $this->entityManager->flush($gallery);                
            }

            return $media;
        }
        
        return null;
    }
    
    function medicalCenterUploadLogo($file, InstitutionMedicalCenter $medicalCenter, $flushObject = true)
    {
        $result = parent::uploadFile($file);
    
        if(is_object($result)) {
            $media = $result;    
            $sizes = $this->getSizesByType(self::LOGO_TYPE_IMAGE);

            $this->deleteMediaAndFiles($medicalCenter->getLogo(), $sizes);

            $medicalCenter->setLogo($media);

            $this->resize($media, $sizes, false);

            if($flushObject) {
                $this->entityManager->persist($medicalCenter);
                $this->entityManager->flush($medicalCenter);
            }
    
            return $media;
        }
    
        return null;
    }

    function medicalCenterUploadToGallery($file, InstitutionMedicalCenter $medicalCenter, $flushObject = true)
    {
        $result = parent::uploadFile($file);
    
        if(is_object($result)) {
            $media = $result;

            $sizes = $this->getSizesByType(self::GALLERY_TYPE_IMAGE);
            $this->resize($media, $sizes);

            $gallery = $this->entityManager->getRepository('MediaBundle:Gallery')->findOneByInstitution($medicalCenter->getInstitution()->getId());

            if(!$gallery) {
                $gallery = new Gallery();
                $gallery->addMedia($media);
                $gallery->setInstitution($medicalCenter->getInstitution());
    
            } else {
                $gallery->addMedia($media);
            }

            $medicalCenter->addMedia($media);

            if($flushObject) {
                $this->entityManager->persist($gallery);
                $this->entityManager->persist($medicalCenter);
                $this->entityManager->flush();                
            }

            return $media;
        }

        return null;
    }

    function delete($media, $imageType = self::GALLERY_TYPE_IMAGE)
    {
        $sizes = $this->getSizesByType($imageType);
    
        parent::deleteMediaAndFiles($media, $sizes);
    }

    public function getSizesByType($imageType)
    {
        return isset($this->imageSizes[$imageType]) ? $this->imageSizes[$imageType] : array();
    }
}