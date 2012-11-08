<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

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
    
    /**
     * Actionn handler for viewing all InstitutionMedicalCenter of selected institution 
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
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
     * First step for creating InstitutionMedicalCenter, this includes adding name and description of the center.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterDetailsAction()
    {
        $service = $this->get('services.institution_medical_center');
        
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
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());
        
                // TODO: fire event
        
                // redirect to step 2;
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addInstitutionTreatments',array(
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
        
        return $this->render('AdminBundle:InstitutionTreatments:addMedicalCenterDetails.html.twig', $params);   
    }
    
    public function addInstitutionTreatmentsAction()
    {
        
    }
}