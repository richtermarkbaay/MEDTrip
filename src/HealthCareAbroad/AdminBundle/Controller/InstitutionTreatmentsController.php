<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\HttpFoundation\Request;

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
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionName' => $this->institution->getName(),
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
            'institutionMedicalCenters' => $this->filteredResult,
            'pager' => $this->pager
        );
        
         return $this->render('AdminBundle:InstitutionTreatments:viewAllMedicalCenters.html.twig', $params);   
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
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'days' => $daysArr
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
//                 var_dump($businessHours);
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
    
    /// @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
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
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        );
        
        return $this->render('AdminBundle:InstitutionTreatments:form.specialization.html.twig', $params);   
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