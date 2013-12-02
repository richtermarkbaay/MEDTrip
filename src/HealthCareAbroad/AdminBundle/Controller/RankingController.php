<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\Form\GenericRankingItemFormType;

class RankingController extends Controller
{
    public function institutionIndexAction(Request $request)
    {
        $institutions = $this->filteredResult;
        
        $rankingItemForm = $this->createForm(new GenericRankingItemFormType());
        
        return $this->render('AdminBundle:Ranking:institutionRankings.html.twig', array(
            //'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS),
            'institutions' => $institutions,
            'rankingItemForm' => $rankingItemForm->createView()
        ));
    }
    
    public function institutionMedicalCenterIndexAction(Request $request)
    {
        $rankingItemForm = $this->createForm(new GenericRankingItemFormType());
        
        return $this->render('AdminBundle:Ranking:institutionMedicalCenterRankings.html.twig', array(
            'institutionMedicalCenters' => $this->filteredResult,
            'rankingItemForm' => $rankingItemForm->createView()
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
    
    /**
     * Update ranking of an institution
     * 
     * @author acgvelarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putUpdateInstitutionRankingAction(Request $request)
    {
        $form = $this->createForm(new GenericRankingItemFormType());
        $form->bind($request);
        $data = $form->getData();
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($data['id']);
        if (!$institution){
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $institution->setTotalClinicRankingPoints($data['rankingPoints']);
        $institutionService = $this->get('services.institution');
        $institutionService->save($institution);
        
        // check if this is a single center institution
        if ($institutionService->isSingleCenter($institution)){
            // also set the ranking point for the clinic
            $firstMedicalCenter = $institutionService->getFirstMedicalCenter($institution);
            if ($firstMedicalCenter){
                $firstMedicalCenter->setRankingPoints($data['rankingPoints']);
                $this->get('services.institution_medical_center')->save($firstMedicalCenter);
            }
        }
        
        
        $responseData = array(
        	'id' => $institution->getId(),
            'rankingPoints' => $institution->getTotalClinicRankingPoints(),
            'error' => false
        );
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * Update ranking of an institution medical center
     * 
     * @author acgvelarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putUpdateInstitutionMedicalCenterRankingAction(Request $request)
    {
        $form = $this->createForm(new GenericRankingItemFormType());
        $form->bind($request);
        $data = $form->getData();
        
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($data['id']);
        if (!$institutionMedicalCenter){
            throw $this->createNotFoundException('Invalid medical center');	
        }
        $institution = $institutionMedicalCenter->getInstitution();
        $institutionMedicalCenter->setRankingPoints($data['rankingPoints']);
        $this->get('services.institution_medical_center')->save($institutionMedicalCenter);
        
        // check if this is a single center institution
        $institutionService = $this->get('services.institution');
        if ($institutionService->isSingleCenter($institution)){
            // also set the ranking point for the parent institution
            $institution->setTotalClinicRankingPoints($data['rankingPoints']);
            $institutionService->save($institution);
        }
        
        $responseData = array(
            'id' => $institutionMedicalCenter->getId(),
            'rankingPoints' => $institutionMedicalCenter->getRankingPoints(),
            'error' => false
        );
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
}