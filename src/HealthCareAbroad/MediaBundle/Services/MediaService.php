<?php
namespace HealthCareAbroad\MediaBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;
use HealthCareAbroad\MediaBundle\Entity\Media;
use HealthCareAbroad\MediaBundle\MediaContext;
use HealthCareAbroad\MediaBundle\Entity\Gallery;
use HealthCareAbroad\MediaBundle\Resizer\Resizer;
use HealthCareAbroad\MediaBundle\Gaufrette\FilesystemManager;
use HealthCareAbroad\MediaBundle\Gaufrette\Adapter\LocalAdapter;

use Gaufrette\File;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * TODO:
 * 1. REFACTOR!!!
 * 2. REFACTOR!!!
 * 3. REFACTOR!!!
 * 4. REFACTOR!!!
 * 5. REFACTOR specially the upload function
 *
 * @author harold
 *
 */
class MediaService
{
    private $entityManager;
    private $filesystemManager;
    private $resizer;

    public function __construct(FilesystemManager $filesystemManager, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->filesystemManager = $filesystemManager;
    }

    public function setResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    public function attachMedia($context)
    {
        $processedContext = $this->processContext($context);

        $media = $processedContext['media'];

        try {
            $entity = $processedContext['entity'];
            $entity->setMedia($processedContext['media']);

            $this->entityManager->persist($entity);
            $this->entityManager->flush($entity);
        } catch (\Exception $e) {
            return false;
        }

        $institution = $processedContext['institution'];

        //TODO: abstract this out
        return '/media/'.$institution->getId().'/thumbnail-'.$media->getName();
    }

    private function processContext($context)
    {
        $processedContext = array();

        switch ($context['context']) {
            case MediaContext::INSTITUTION_LOGO:
                $institution = $this->entityManager->getRepository('InstitutionBundle:Institution')->find($context['id']);

                $processedContext['entity'] = $institution;
                $processedContext['institution'] = $institution;

                break;
        }

        $processedContext['media'] = $this->entityManager->getRepository('MediaBundle:Media')->find($context['mediaId']);

        return $processedContext;
    }

    private function getEntityFromContext($context)
    {
        $entity = null;

        switch ($context['context']) {
            case MediaContext::INSTITUTION_LOGO:
                $entity = $this->entityManager->getRepository('InstitutionBundle:Institution')->find($context['id']);
                break;
        }

        return $entity;
    }

    public function addMedia(UploadedFile $file, $institutionId)
    {
        if (!$file->isValid()) {
            return $file->getError();
        }
        $filesystem = $this->filesystemManager->get($institutionId, 'local');

        //TODO: rename/sanitize filename
        $filename = $file->getClientOriginalName();

        $media = new Media();
        $media->setName($filename);
        $media->setContentType($file->getMimeType());
        //TODO: the ff are temporary
        $media->setCaption($filename);
        $media->setContext($institutionId);
        $media->setUuid(\time());

        //TODO: ignore the other attributes for now
        $proceed = true;
        try {
            $file->move($this->filesystemManager->getAdsUploadRootDir(), $filename);
        } catch (FileException $e) {
            $proceed = false;
        }

        if ($proceed) {
            $this->entityManager->persist($media);
            $this->entityManager->flush();
        }
        unset($file);

        return $media;
    }

    public function upload(UploadedFile $file, $institutionId, $context = array())
    {
        if (!$file->isValid()) {
            return $file->getError();
        }
        $filesystem = $this->filesystemManager->get($institutionId, 'local');

        //TODO: rename/sanitize filename
        $filename = time().'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $caption = $file->getClientOriginalName();

        $proceed = true;
        try {
            $file->move($this->filesystemManager->getUploadRootDir(), $filename);
        } catch (FileException $e) {
            $proceed = false;
        }

        if ($proceed) {
            $imageAttributes = getimagesize($this->filesystemManager->getUploadRootDir().'/'.$filename);

            $media = new Media();
            $media->setName($filename);
            $media->setContentType($imageAttributes['mime']);
            $media->setCaption($caption);
            $media->setContext($institutionId);
            $media->setUuid(time());
            $media->setWidth($imageAttributes[0]);
            $media->setHeight($imageAttributes[1]);
            //TODO: ignore the other attributes for now

            $in = new File($filename, $filesystem);
            $out = new File('thumbnail-'.$filename, $filesystem);

            $format = image_type_to_extension($imageAttributes[2], false);

            //TODO: inject this dynamically selecting the optimal ImagineInterface available
            //$resizer = new SquareResizer(new \Imagine\Gd\Imagine());
            //$resizer->resize($media, $in, $out, $format, array('width' => 180, 'height' => 180));
            $this->resizer->resize($media, $in, $out, $format, array('width' => 180, 'height' => 180));

            $gallery = $this->entityManager->getRepository('MediaBundle:Gallery')->find($institutionId);

            if (is_null($gallery)) {
                $gallery = new Gallery();
                $gallery->setInstitution($this->entityManager->getRepository('InstitutionBundle:Institution')->find($institutionId));
            }

            $gallery->addMedia($media);

            $mediaEntity = null;

            if (!empty($context)) {
                switch ($context['context']) {
                    case 'institutionMedicalCenter':
                        $mediaEntity = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($context['contextId']);

                        break;
                }
            }

            if ($mediaEntity) {
                $mediaEntity->addMedia($media);
                $this->entityManager->persist($mediaEntity);
            }

            //TODO: set cascade persist on entity Gallery
            $this->entityManager->persist($media);
            $this->entityManager->persist($gallery);
            $this->entityManager->flush();
        }

        $errorCode = $file->getError();
        unset($file);

        return $errorCode;
    }

    public function retrieveAllMedia($institutionId)
    {
        $gallery = $this->entityManager->getRepository('MediaBundle:Gallery')->find($institutionId);

        return $gallery->getMedia();
    }

    public function retrieveMedia($mediaId, $institutionId)
    {
        return $this->entityManager->getRepository('MediaBundle:Media')->findWithInstitutionId($mediaId, $institutionId);
    }

    public function editMediaCaption($mediaId, $institutionId, $caption)
    {
        $media = $this->retrieveMedia($mediaId, $institutionId);

        return $this->editMedia($media, array('caption' => $caption));
    }

    public function editMedia(Media $media, array $fields = array())
    {
        //TODO: rest of the fields
        if (isset($fields['caption'])) {
            $media->setCaption($fields['caption']);
        }

        $this->entityManager->persist($media);
        $this->entityManager->flush($media);

        return $media;
    }

    /**
     * TODO: delete the physical file itself?
     */
    public function delete($mediaId, $institutionId)
    {
        $success = 0;

        $media = $this->retrieveMedia($mediaId, $institutionId);

        if ($media) {

                $this->entityManager->remove($media);

                try {

                    $this->entityManager->flush();
                    $success = 1;
                } catch(\Exception $e) {
                    var_dump($e);
                }
        }

        return $success;
    }

    public function deleteMedia(Media $media)
    {
        $this->entityManager->remove($media);
    }

    public function addMedicalCenterMedia($institutionMedicalCenterId, $mediaId)
    {
        $success = 1;

        try {
            $center = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($institutionMedicalCenterId);
            $media = $this->entityManager->getRepository('MediaBundle:Media')->find($mediaId);

            $center->addMedia($media);

            $this->entityManager->persist($center);
            $this->entityManager->flush($center);
        } catch (Exception $e) {
            $success = 0;
        }

        return $success;
    }
    public function addAdvertisementMedia(Advertisement $advertisement, $media)
    {
        $success = 1;

        try {
            $advertisement->addMedia($media);

            $this->entityManager->persist($advertisement);
            $this->entityManager->flush($advertisement);
        } catch (Exception $e) {
            $success = 0;
        }

        return $success;
    }
}