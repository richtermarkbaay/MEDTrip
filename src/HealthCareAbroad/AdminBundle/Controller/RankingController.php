<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RankingController extends Controller
{
    public function institutionIndexAction(Request $request)
    {
        $institutions = $this->get('services.institution.factory')->findAllApproved();
        $data = array();
        foreach ($institutions as $_each) {
            $data[] = array(
                'id' => $_each->getId(),
                'label' => $_each->getName()
            );
        }
        
        return $this->render('AdminBundle:Ranking:form.index.html.twig', array(
                        'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS),
                        'institutions' => $this->filteredResult,
                        'pager' => $this->pager,
                        'isInstitution' => true,
                        'page' => 'rank_institution_page',
                        'page_uri' => 'admin_institution_ranking_index',
        ));
    }
    
    public function institutionMedicalCenterIndexAction(Request $request)
    {
        $institutions = $this->get('services.institution.factory')->findAllApproved();
        
        $data = array();
        foreach ($institutions as $_each) {
            $data[] = array(
                            'id' => $_each->getId(),
                            'label' => $_each->getName()
            );
        }
        
        return $this->render('AdminBundle:Ranking:form.index.html.twig', array(
                        'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS),
                        'centers' => $this->filteredResult,
                        'pager' => $this->pager,
                        'page' => 'rank_center_page',
                        'page_uri' => 'admin_center_ranking_index',
        
        ));
    }
    
    public function ajaxSearchInstitutionMedicalCenterAction(Request $request)
    {
        $institutionId = $request->get('institutionId',0);
        $countryId = $request->get('_countryId',0);
        $cityId = $request->get('_cityId',0);
        $imcId = $request->get('imcId',0);
        
        $centers = array();
        if($imcId == 0 && $institutionId != 0) {
            $centers = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy(array('institution' => $institutionId, 'status' => InstitutionMedicalCenterStatus::APPROVED));
        } 
        else if($imcId != 0) {
            $centers = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy(array('id' => $imcId));
        }
        else {
            if($institutionSearchName = $request->get('institutionName')) {
                $params = array('countryId' => $countryId,
                              'cityId' => $cityId,
                              'searchTerm' => $institutionSearchName);
                
                $centers = $this->get('services.institution_medical_center')->getApprovedMedicalCentersByFiltersAndInstitutionSearchName($params);
            }
        }
        $html = $this->renderView('AdminBundle:Ranking/Partials:view.html.twig', array('centers' => $centers));
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        
    }
    
    public function ajaxSearchInstitutionAction(Request $request)
    {
        $institutionId = $request->get('institutionId',0);
        $countryId = $request->get('_countryId',0);
        $cityId = $request->get('_cityId',0);
        $params = array();
        if($institutionId != 0) {
            $institutions = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->findBy(array('id' => $institutionId));
            $params = array('institutions' => $institutions,
                            'isInstitution' => true);
        } 
        else {
            if($institutionSearchName = $request->get('institutionName')) {
                $data = array('countryId' => $countryId,
                                'cityId' => $cityId,
                                'searchTerm' => $institutionSearchName);
                $institutions = $this->get('services.institution')->getAllInstitutionByParams($data);
                
                $params = array('institutions' => $institutions,
                                'isInstitution' => true);
            }
        }
        $html = $this->renderView('AdminBundle:Ranking/Partials:view.html.twig', $params);
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
    public function ajaxUpdateInstitutionRankingAction(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
        
        $currentRankingPts = $institution->getTotalClinicRankingPoints();
        $institution->setTotalClinicRankingPoints( $type == 'inc' ? ($currentRankingPts + 1) : (($currentRankingPts != 0) ? ($currentRankingPts - 1) : NULL));
        $this->save($institution);
        
        $response = array('data' => 'success', 'points' => $institution->getTotalClinicRankingPoints() ? $institution->getTotalClinicRankingPoints() : 0 );
        
        return new Response(\json_encode($response), 200, array('content-type' => 'application/json'));
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
        $center->setRankingPoints( $type == 'inc' ? ($currentRankingPts + 1) : (($currentRankingPts != 0) ? ($currentRankingPts - 1) : NULL));
        $this->save($center);
        
        $response = array('data' => 'success', 'points' => $center->getRankingPoints() ? $center->getRankingPoints() : 0);
        
        return new Response(\json_encode($response), 200, array('content-type' => 'application/json'));
    }
    
}