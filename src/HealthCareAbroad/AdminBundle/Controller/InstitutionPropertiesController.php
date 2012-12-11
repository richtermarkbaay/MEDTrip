<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionPropertiesController extends Controller
{
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function preExecute()
    {
        $this->institutionService = $this->get('services.institution');
        $this->institution = $this->get('services.institution.factory')->findById($this->getRequest()->get('institutionId', 0));
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
    }
    
    public function addAncilliaryServiceAction(Request $request)
    {
//         $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id');
//         var_dump($form->getData());exit;
//         $property = new InstitutionProperty();
//         $property->setInstitution($this->institution);
//         $property->
//         $offeredServicesArray = $this->getRequest()->get('offeredServicesData');
        
        $property = new InstitutionProperty();
        $property = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id')->getData();
        $property->setValue($this->getRequest()->get('offeredServicesData'));
        var_dump($property);exit;
    }
    
    public function addLanguageSpokenAction(Request $request)
    {
        $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'language_id');
        $formActionUrl = $this->generateUrl('admin_institution_addLanguageSpoken', array('institutionId' => $this->institution->getId()));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->get('services.institution_property')->save($form->getData());
        
                return $this->redirect($formActionUrl);
            }
        }
        
        $params = array(
            'formAction' => $formActionUrl,
            'form' => $form->createView()
        );
        return $this->render('AdminBundle:InstitutionProperties:common.form.html.twig', $params);
    }
}