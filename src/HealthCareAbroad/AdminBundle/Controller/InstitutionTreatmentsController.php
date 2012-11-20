<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionAffiliationFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for handling actions related to InstitutionMedicalCenter and treatments
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionTreatmentsController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
        
        // check InstitutionMedicalCenter
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }

        }
    }
    
    
    public function viewAllMedicalCentersAction()
    {
        $criteria = array('institution' => $this->institution);
        $institutionMedicalCenters = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy($criteria);
        
        $params = array(
            'institution' => $this->institution,
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
            'institutionMedicalCenters' => $institutionMedicalCenters,
            'pager' => $this->pager
        );
        
         return $this->render('AdminBundle:InstitutionTreatments:viewAllMedicalCenters.html.twig', $params);   
    }

    /**
     * Actionn handler for viewing all InstitutionMedicalCenter of selected institution
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewMedicalCenterAction()
    {
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);
		
        $affiliations = $this->getDoctrine()->getRepository('HelperBundle:Affiliation')->getInstitutionAffiliations($this->institutionMedicalCenter->getId());
        
        $params = array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'specializations' => $specializations,
        	'affiliations' => $affiliations,
            'selectedSubMenu' => 'centers'
            //'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            //'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions()
        );
    
        return $this->render('AdminBundle:InstitutionTreatments:viewMedicalCenter.html.twig', $params);
    }

    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterAction()
    {
        $service = $this->get('services.institution_medical_center');
        $request = $this->request;
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new institutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            // there is an imcId in the Request, check if this is a draft
            if ($this->institutionMedicalCenter && !$service->isDraft($this->institutionMedicalCenter)) {
                
                $request->getSession()->setFlash('error', 'Invalid medical center draft.');
                
                return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
            }
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionMedicalCenter);
        if ($request->isMethod('POST')) {
            $form->bind($this->request);
            
            // Get contactNumbers and convert to json format
            $businessHours = json_encode($request->get('businessHours'));
            
            if ($form->isValid()) {
                
                // Set BusinessHours before saving
                $form->getData()->setBusinessHours($businessHours);
                
                $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());

                $request->getSession()->setFlash('success', '"' . $this->institutionMedicalCenter->getName() . '"' . " has been created. You can now add Specializations to this center.");
                
                // TODO: fire event

                // redirect to step 2;
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addSpecialization',array(
                    'institutionId' => $this->institution->getId(),
                    'imcId' => $this->institutionMedicalCenter->getId()
                )));
            }
        }
        
        $daysArr = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $params = array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'selectedSubMenu' => 'centers'
        );
        
        return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);   
    }

    public function editMedicalCenterAction()
    {
        $service = $this->get('services.institution_medical_center');
        $request = $this->request;
        
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new institutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            // there is an imcId in the Request, check if this is a draft
            if ($this->institutionMedicalCenter && !$service->isDraft($this->institutionMedicalCenter)) {
    
                $this->request->getSession()->setFlash('error', 'Invalid medical center draft.');
    
                return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
            }
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionMedicalCenter);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            // Get contactNumbers and convert to json format
            $businessHours = json_encode($request->get('businessHours'));
            
            if ($form->isValid()) {
                // Set BusinessHours before saving
                $form->getData()->setBusinessHours($businessHours);
                
                $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());
        
                $request->getSession()->setFlash('success', '"' . $this->institutionMedicalCenter->getName() . '"' . " has been updated. You can now add Specializations to this center.");
    
                // TODO: fire event    
                // redirect to step 2;
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addSpecialization',array(
                                'institutionId' => $this->institution->getId(),
                                'imcId' => $this->institutionMedicalCenter->getId()
                )));
            }
        }
        
        $params = array(
                        'form' => $form->createView(),
                        'institutionId' => $this->institution->getId(),
                        'institutionMedicalCenter' => $this->institutionMedicalCenter
        );
    
        return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMedicalCenterAction()
    {
        if($this->request->get('description')) {
            $description = $this->request->get('description'); 
            $this->institutionMedicalCenter->setDescription($description);
        }
        
        if($this->request->get('name')) {
            $name = $this->request->get('name'); 
            $this->institutionMedicalCenter->setName($name);
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $result = $em->flush();

        $response = new Response (json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;        
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function updateMedicalCenterStatusAction()
    {
        $request = $this->getRequest();
        $status = $request->get('status');
    
        $redirectUrl = $this->generateUrl('admin_institution_manageCenters', array('institutionId' => $request->get('institutionId')));
    
        if(!InstitutionMedicalCenterStatus::isValid($status)) {
            $request->getSession()->setFlash('error', "Unable to update status. $status is invalid status value!");
    
            return $this->redirect($redirectUrl);
        }

        $this->institutionMedicalCenter->setStatus($status);
    
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $em->flush($this->institutionMedicalCenter);

        // dispatch EDIT institutionMedicalCenter event
        $actionEvent = InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $request->get('institutionId')));
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);
    
        $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" status has been updated!');
    

        return $this->redirect($redirectUrl);
    }

    /**
     * 
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function centerSpecializationsAction()
    {
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        $params = array('specializations' => $specializations);

        return $this->render('AdminBundle:InstitutionTreatments:centerSpecializations.html.twig', $params);
    }   

    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationAction()
    {
        $service = $this->get('services.institution_medical_center');
        
        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid institutionMedicalCenter');
        }
        
        $institutionSpecializationForm = new InstitutionSpecializationFormType($this->institution);
        
        $form = $this->createForm($institutionSpecializationForm, new InstitutionSpecialization());
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {

                $institutionSpecialization = $form->getData();
                $institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
                $institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($institutionSpecialization);
                $em->flush();

                if($institutionSpecialization->getId() && count($treatmentIds = $this->request->get('treatments'))) {
                    $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->updateTreatments($institutionSpecialization->getId(), $treatmentIds);
                }

                $this->request->getSession()->setFlash('success', "Specialization has been saved!");

                // TODO: fire event

                // redirect to step 2;
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addSpecialization',array(
                    'institutionId' => $this->institution->getId(),
                    'imcId' => $this->institutionMedicalCenter->getId()
                )));
            }
        }
        
        $params = array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'selectedSubMenu' => 'centers'
        );
        
        return $this->render('AdminBundle:InstitutionTreatments:addSpecializations.html.twig', $params);   
    }
    
    /**
     *
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function addAffiliationsAction()
    {
    	$form = $this->createForm(new InstitutionAffiliationFormType(),$this->institutionMedicalCenter);
    	
    	 if ($this->request->isMethod('POST')) {
    		 
    		$form->bind($request);
    	
    		if ($form->isValid()) {
    	
    			$this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
    			->saveAsDraft($form->getData());
    			$request->getSession()->setFlash('success', 'Affiliations has been saved!');
    			
    			return $this->redirect($this->generateUrl('AdminBundle:InstitutionTreatments:viewMedicalCenter.html.twig',
    							array('imcId' => $this->institutionMedicalCenter->getId(), 'institutionId' => $this->institution->getId())));
    		}
    	}
    	 
    	return $this->render('AdminBundle:InstitutionTreatments:addAffiliation.html.twig', array(
    					'form' => $form->createView(),
    					'institutionMedicalCenter' => $this->institutionMedicalCenter,
    					'formAction' => $this->generateUrl('admin_institution_medicalCenter_addAffiliations', 
    									array('institutionId:' => $this->institution->getId() , 'imcId:' => $this->institutionMedicalCenter->getId()))
    	));
    }
    
    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editSpecializationAction()
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($this->request->get('isId'));
        $institutionTreatments = $institutionSpecialization->getTreatments();
        $institutionTreatmentIds = array();
        foreach($institutionTreatments as $treatment) {
            $institutionTreatmentIds[] = $treatment->getId();
        }

        $institutionSpecializationForm = new InstitutionSpecializationFormType($this->institution);
        $form = $this->createForm($institutionSpecializationForm, $institutionSpecialization);

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
    
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($form->getData());
                $em->flush();

                if($institutionSpecialization->getId()) {
                    $treatmentIds = $this->request->get('treatments', array());
                    $deleteTreatmentsIds = array_diff($institutionTreatmentIds, $treatmentIds);

                    $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
                    $instSpecializationRepo->updateTreatments($institutionSpecialization->getId(), $treatmentIds, $deleteTreatmentsIds);
                }

                $this->request->getSession()->setFlash('success', "Specialization has been saved!");

                // TODO: fire event

                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_editSpecialization',array(
                    'institutionId' => $this->institution->getId(),
                    'isId' => $institutionSpecialization->getId()
                )));
            }
        }

        $params = array(
            'form' => $form->createView(),
            'institutionSpecialization' => $institutionSpecialization,
            'institutionId' => $this->institution->getId(),
            'institutionTreatmentIds' => $institutionTreatmentIds
        );

        return $this->render('AdminBundle:InstitutionTreatments:editSpecialization.html.twig', $params);
    }

    /**
     * Add specialization and treatments to an institution medical center
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInstitutionTreatmentsAction()
    {
        $service = $this->get('services.institution_medical_center');
        // this should only be accessed by draft
        if (!$service->isDraft($this->institutionMedicalCenter)) {
            $this->request->getSession()->setFlash('error', 'Invalid medical center draft.');
            
            // return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
        }
        
        $institutionSpecialization = new InstitutionSpecialization();
        
        $params = array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        );
        
        return $this->render('AdminBundle:InstitutionTreatments:addInstitutionTreatments', $params);
    }

}