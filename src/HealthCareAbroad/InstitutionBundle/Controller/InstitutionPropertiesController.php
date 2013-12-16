<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use Doctrine\Tests\DBAL\Types\VarDateTimeTest;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionPropertiesController extends InstitutionAwareController
{
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    /**
     * @var Request
     */
    private $request;
    
    public function preExecute()
    {
        parent::preExecute();
        $this->institutionService = $this->get('services.institution');
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
        
        $this->request = $this->getRequest();
    }
    
    public function ajaxEditGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('propertyId', 0));
        $propertyType = $this->get('services.institution_property')->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }

        $globalAward = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($this->request->get('globalAwardId'));
        
        if (!$globalAward) {
            throw $this->createNotFoundException('Invalid global award.');
        }
        $property->setValueObject($globalAward);

        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType(), $property);
        
        if ($request->isMethod('POST')) {
            $editGlobalAwardForm->bind($request);

            if ($editGlobalAwardForm->isValid()) {

                try {
                    $property = $editGlobalAwardForm->getData();

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($property);
                    $em->flush();

                    // Invalidate InstitutionProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                    $output = array('status' => true, 'extraValue' => $property->getExtraValue());
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Invalid Form', 400);
            }
        }
        
        return $response;
    }
    
    /**
     * Remove institution global award
     * 
     * @param Request $request
     */
    public function ajaxRemoveGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('id', 0));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
    
       $form = $this->createForm(new CommonDeleteFormType(), $property);
        
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
        
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($property);
                $em->flush();

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                $response = new Response(\json_encode(array('id' => $request->get('id', 0))), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
        
        return $response;
    }
    
    /**
     * Add an ancillary service to institution
     * Required parameters:
     *     - institutionId
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author alniejacobe
     */
    public function ajaxAddAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
        ->find($request->get('id', 0));
    
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
        // check if this institution already have this property value
        if ($this->institutionService->hasPropertyValue($this->institution, $propertyType, $ancillaryService->getId())) {
            $response = new Response("Property value {$ancillaryService->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionPropertyByName($propertyType->getName(), $this->institution);
            $property->setValue($ancillaryService->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                $output = array(
                    'label' => 'Delete Service',
                    'href' => $this->generateUrl('institution_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(), 'id' => $property->getId() )),
                    '_isSelected' => true,
                );
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
    
        }
    
        return $response;
    }
    
    /**
     * Remove an ancillary service to institution
     * Required parameters:
     *     - institutionId
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author alniejacobe
     */
    public function ajaxRemoveAncillaryServiceAction(Request $request)
    {
      $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('id', 0));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
        
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($property->getValue());
    
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();

            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

            $output = array(
                    'label' => 'Add Service',
                    'href' => $this->generateUrl('institution_ajaxAddAncillaryService', array('institutionId' => $this->institution->getId(), 'id' => $ancillaryService->getId() )),
                    '_isSelected' => false,
            );
            
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
    }
}