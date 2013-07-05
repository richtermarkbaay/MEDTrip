<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\HelperBundle\Form\PageMetaConfigurationFormType;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RankingController extends Controller
{
    public function indexAction(Request $request)
    {
        // load approved institutions by default
        $institutions = $this->get('services.institution.factory')->findAllApproved();
        $data = array();
        foreach ($institutions as $_each) {
            $data[] = array(
                'id' => $_each->getId(),
                'label' => $_each->getName()
            );
        }
        
        return $this->render('AdminBundle:Ranking:form.search.html.twig', array(
                        'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS)
        ));
    }
    
    public function ajaxSearchInstitutionAction(Request $request)
    {
        $institutionId = $request->get('institutionId',0);
        $imcId = $request->get('imcId',0);
        $params = array();
        if($imcId != 0 && $institutionId != 0) {
            $center = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
            $params = array('center' => $center);
        } 
        else {
            if($institutionSearchName = $request->get('institutionName')) {
                $institutions = $this->get('services.institution')->getAllInstitutionBySearhTerm($institutionSearchName);
                $params = array('institutions' => $institutions,
                                'isArray' => true);
            }
        }
        $html = $this->renderView('AdminBundle:Ranking:view.html.twig', $params);
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        
    }
    
    public function ajaxUpdateInstitutionRankingAction(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
        $currentRankingPts = $institution->getTotalClinicRankingPoints();
        $institution->setTotalClinicRankingPoints( $type == 'inc' ? ($currentRankingPts + 1) : (($currentRankingPts > 0) ? ($currentRankingPts - 1) : NULL));
        $this->save($institution);
        
        return new Response(\json_encode(array('html' => 'success')), 200, array('content-type' => 'application/json'));
    }
    
    private function save($entity) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
    
    public function ajaxUpdateInstitutionMedicalCenterRankingAction(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        $center = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($id);
        $currentRankingPts = $center->getRankingPoints();
        $center->setRankingPoints( $type == 'inc' ? ($currentRankingPts + 1) : (($currentRankingPts > 0) ? ($currentRankingPts - 1) : NULL));
        $this->save($center);
        
        return new Response(\json_encode(array('html' => 'success')), 200, array('content-type' => 'application/json'));
    }
    
}