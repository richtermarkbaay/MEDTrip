<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaGalleryController extends InstitutionAwareController 
{
    public function preExecute()
    {
        parent::preExecute();
    }
    
    public function indexAction(Request $request) 
    {
        $photos = $request->get('medical-center-id') 
            ? $this->get('services.institution.gallery')->getInstitutionMedicalCenterPhotos($request->get('medical-center-id'))
            : $this->get('services.institution.gallery')->getInstitutionPhotos($this->institution->getId());

        $params = array(
            'photos' => $photos,
            'institution' => $this->institution,
            'medicalCenters' => $this->institution->getInstitutionMedicalCenters(),
            'photosLinkedToMedicalCenter' => $this->get('services.institution.gallery')->getPhotosLinkedToMedicalCenter($this->institution->getId())
        );

        return $this->render('InstitutionBundle:MediaGallery:index.html.twig', $params);
    }
    
    public function uploadAction(Request $request)
    {
        $result = array('status' => false);
        if($file = $request->files->get('file')) {
            $media = $this->get('services.institution.media')->uploadToGallery($file, $this->institution, true);
            if($media) {
                $result['status'] = true;
                $result['mediaId'] = $media->getId();               

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
            }
        }
        
        $request->getSession()->setFlash('success', "Photo has been successfully uploaded!");

        return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
    }
    
    public function linkFileToClinicsAction(Request $request)
    {
        $mediaIds = explode(',', $request->get('media_ids')); 
        $medicalCenterIds = $request->get('medical_center_ids', array());
        
        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->getMediaByIds($mediaIds);

        foreach($this->institution->getInstitutionMedicalCenters() as $each) {
            if(in_array($each->getId(), $medicalCenterIds)) {
                foreach($media as $medium) {
                    $each->addMedia($medium);
                    $em->persist($each);
                }

                // Invalidate InstitutionMedicalCenterProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($each->getId()));
            }
        }
        $em->flush();
        
        // Invalidate InstitutionProfile memcache
        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

        $request->getSession()->setFlash('success', "Photo has been successfully uploaded and linked to clinics!");

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    public function updateMediaAction(Request $request)
    {
        $result = false;
        $mediaData = $request->get('media');
        $newMedicalCenterIds = $request->get('new-medical-center-ids') ? explode(',', $request->get('new-medical-center-ids')) : array();
        $removeMedicalCenterIds = $request->get('remove-medical-center-ids') ? explode(',', $request->get('remove-medical-center-ids')) : array();

        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->find($mediaData['id']);

        if($media) {
            $result = true;
            $media->setCaption($mediaData['caption']);
            $em->persist($media);

            foreach($this->institution->getInstitutionMedicalCenters() as $each) {
                if(in_array($each->getId(), $newMedicalCenterIds)) {
                    $each->addMedia($media);
                    $em->persist($each);

                    // Invalidate InstitutionMedicalCenterProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($each->getId()));
                }

                if(in_array($each->getId(), $removeMedicalCenterIds)) {
                    $each->removeMedia($media);
                    $em->persist($each);

                    // Invalidate InstitutionMedicalCenterProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($each->getId()));
                }
            }
            $em->flush();
            
            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

            $request->getSession()->setFlash('success', "Photo has been updated!");
        }

        return $this->redirect($this->generateUrl('institution_mediaGallery_index'));
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->find($request->get('mediaId'));
        $stringCenterIds = $request->get('institutionMedicalCenterIds', '');

        $em->remove($media);
        $em->flush();

        // Invalidate InstitutionProfile memcache
        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

        // Invalidate InstitutionMedicalCenterProfile memcache if any
        if($stringCenterIds) {
            $centerIds = explode(',', $stringCenterIds);
            foreach($centerIds as $centerId) {
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($centerId));
            }
        }

        $result['status'] = true;
        $result['message'] = 'Photo has been deleted!';

        return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
    }
    
}