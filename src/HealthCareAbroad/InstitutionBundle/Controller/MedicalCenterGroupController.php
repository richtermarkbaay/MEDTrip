<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterGroupService;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterGroupRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterGroupFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for InstitutionMedicalCenterGroup.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class MedicalCenterGroupController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenterGroup
     */
    private $institutionMedicalCenterGroup = null;
    
    /**
     * @var InstitutionMedicalCenterGroupRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterGroupService
     */
    private $service;
    
    public function preExecute()
    {
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterGroup');
        $this->service = $this->get('services.institution_medical_center_group');
        
        if ($imcgId=$this->getRequest()->get('imcgId',0)) {
            $this->institutionMedicalCenterGroup = $this->repository->find($imcgId);
            
            // non-existent medical center group
            if (!$this->institutionMedicalCenterGroup) {
                return $this->_redirectIndexWithFlashMessage('Invalid medical center group.', 'error');
            }
            
            // medical center group does not belong to this institution
            if ($this->institutionMedicalCenterGroup->getInstitution()->getId() != $this->institution->getId()) {
                return $this->_redirectIndexWithFlashMessage('Invalid medical center group.', 'error');
            }
        }
    }
    
    public function indexAction(Request $request)
    {
           
    }
    
    /**
     * Add details of a InstitutionMedicalCenterGroup
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenterGroup)) {
            $this->institutionMedicalCenterGroup = new InstitutionMedicalCenterGroup();
            $this->institutionMedicalCenterGroup->setInstitution($this->institution);
        }
        else 
            if ($this->institutionMedicalCenterGroup && !$this->service->isDraft($this->institutionMedicalCenterGroup)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center group', 'error');
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterGroupFormType(),$this->institutionMedicalCenterGroup);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenterGroup = $this->get('services.institutionMedicalCenterGroup')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenterGroup_addSpecializations',array('imcgId' => $this->institutionMedicalCenterGroup->getId())));
            }
        }
        
        return $this->render('InstitutionBundle:MedicalCenterGroup:addDetails.html.twig', array('form' => $form->createView(), 'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup));
    }
    
    public function addSpecializationsAction(Request $request)
    {
        if (!$this->service->isDraft($this->institutionMedicalCenterGroup)) {
            
            return $this->_redirectIndexWithFlashMessage('Invalid draft medical center group', 'error');
        }
        
        return $this->render('InstitutionBundle:MedicalCenterGroup:addSpecializations.html.twig', array('institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup));
    }
    
    public function editAction(Request $request)
    {
        
    }
    
    public function saveAction(Request $request)
    {
        
    }
    
    /**
     * Convenience function to redirect to medical center group index page with flash notice
     * 
     * @param string $flashMessage
     * @param string $type
     * @param string $redirectRoute
     */
    private function _redirectIndexWithFlashMessage($flashMessage, $type='success')
    {
        $this->getRequest()->getSession($type, $flashMessage);
        
        return $this->redirect($this->generateUrl('institution_medicalCenterGroup_index'));
    }
}