<?php
/**
 *
 * @author harold
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\MediaBundle\Services;

use Imagine\Gd\Imagine;
use Gaufrette\File;
use Gaufrette\Filesystem;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MediaBundle\Entity\Media;
use HealthCareAbroad\MediaBundle\Resizer\DefaultResizer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


abstract class MediaService
{
    protected $resizer;

    protected $filesystem;
    protected $entityManager;
    protected $uploadDirectory;

    abstract protected function setFilesystem(Filesystem $filesystem);
    abstract protected function setEntityManager(EntityManager $entityManager);
    abstract protected function setUploadDirectory($directory); 


    public function __construct()
    {
        $adapter = new Imagine();
        $this->resizer = new DefaultResizer($adapter);
    }

    
    /**
     * Upload a single file
     * @param UploadedFile $file
     * @return \HealthCareAbroad\MediaBundle\Entity\Media|unknown
     */
    public function uploadFile(UploadedFile $file)
    {
        if (!$file->isValid()) {
            return $file->getError();
        }

        $caption = $file->getClientOriginalName();
        $filename = $this->generateUniqueFilename($file);

        $file->move($this->uploadDirectory, $filename);
        $imageAttributes = getimagesize($this->uploadDirectory .'/'. $filename);

        $media = new Media();
        $media->setName($filename);
        $media->setContentType($imageAttributes['mime']);
        $media->setCaption($caption);
        $media->setContext(0);
        $media->setUuid(time());
        $media->setWidth($imageAttributes[0]);
        $media->setHeight($imageAttributes[1]);

        $this->entityManager->persist($media);
        $this->entityManager->flush($media);

        return $media;
    }


    /**
     * Resize Image Media file. Sizes is based on ImageSizes class.
     * @param Media $media
     * @param array $sizes 
     * @param bool $forceCrop
     */
    function resize(Media $media, $sizes = array(), $forceCrop = true)
    {
        if(count($sizes)) {
            $in = new File($media->getName(), $this->filesystem);

            $fileArr = explode(".", $in->getName());
            $format = array_pop($fileArr);

            if(!$forceCrop) {
                $this->resizer->setModeToInset();
            }

            foreach($sizes as $each) {
                $out = new File($each. '_' . $media->getName(), $this->filesystem);
                $imageSize = ImageSizes::toArray($each);

                $this->resizer->resize($media, $in, $out, $format, $imageSize);
            }
        }
    }


    /**
     * 
     * @param UploadedFile $file
     * @return string Unique filename based on microtime
     */
    private function generateUniqueFilename(UploadedFile $file)
    {
        return uniqid() . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    }


    /**
     * Delete Media and removed all files related to the media
     * @param Media $media object
     * @param array $sizes Other image sizes of the media
     * @return boolean
     */
    public function deleteMediaAndFiles($media, $sizes = array())
    {
        $result = false;

        if ($media) {

            $mediaName = $media->getName();

            $this->entityManager->remove($media);
            try {
                $this->entityManager->flush();
                //$this->filesystem->delete($mediaName);
                
                foreach($sizes as $each) {
                    $key = $each .'_'. $mediaName;
                    if($this->filesystem->has($key)) {
                        $this->filesystem->delete($key);                        
                    }
                }

                $result = true;
            } catch(\Exception $e) {
                $result = false;
                var_dump($e);
            }
        }

        return $result;
    }
}