<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

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
        $params = array(
            'institution' => $this->institution, 
            'medicalCenters' => $this->institution->getInstitutionMedicalCenters(), 
            'mediaClinics' => array()
        );

        foreach($params['medicalCenters'] as $each) {
            foreach($each->getMedia() as $media) {
                $params['mediaClinics'][$media->getId()][] = $each->getId();
            }
        }

        return $this->render('InstitutionBundle:MediaGallery:index.html.twig', $params);
    }
    
    public function uploadAction(Request $request)
    {
        $result = array();
        if($file = $request->files->get('file')) {
            $media = $this->get('services.institution.media')->uploadToGallery($file, $this->institution, true);
            $result['mediaId'] = $media->getId();
        }

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
            }
        }

        $em->flush();
        
        return $this->redirect($this->generateUrl('institution_mediaGallery_index'));
    }

    public function updateMediaAction(Request $request)
    {
        $mediaData = $request->get('media');

        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->getMediaByIds($mediaData['id']);

        var_dump($mediaData);
        exit;

        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->getMediaByIds($mediaIds);
    
        foreach($this->institution->getInstitutionMedicalCenters() as $each) {
            if(in_array($each->getId(), $medicalCenterIds)) {
                foreach($media as $medium) {
                    $each->addMedia($medium);
                    $em->persist($each);
                }
            }
        }
    
        $em->flush();
    
        return $this->redirect($this->generateUrl('institution_mediaGallery_index'));
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
        $media = $em->getRepository('MediaBundle:Media')->find($request->get('mediaId'));
        $em->remove($media);
        $em->flush();

        return new Response(json_encode(true), 200, array('Content-Type' => 'application/json'));
    }
    
}