<?php

namespace HealthCareAbroad\TermBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use HealthCareAbroad\TreatmentBundle\Services\TreatmentBundleService;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

class TermTwigExtension extends \Twig_Extension
{
    /**
     * @var TreatmentBundleService
     */
    private $treatmentBundleService;
    
    /**
     * @var Router
     */
    private $router;
    
    public function getFunctions()
    {
        return array(
            'get_document_type_constant' => new \Twig_Function_Method($this, 'getDocumentTypeConstant'),
            'term_document_vo' => new \Twig_Function_Method($this, 'termDocumentValueObject')
        );
    }
    
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }
    
    public function setTreatmentBundleService(TreatmentBundleService $service)
    {
        $this->treatmentBundleService = $service;
    }
    
    public function getDocumentTypeConstant($documentType)
    {
        $documentTypes = array(
            'specialization' => TermDocument::TYPE_SPECIALIZATION,
            'sub_specialization' => TermDocument::TYPE_SUBSPECIALIZATION,
            'treatment' => TermDocument::TYPE_TREATMENT
        );
        
        return \array_key_exists($documentType, $documentTypes) ? $documentTypes[$documentType] : 0;
    }
    
    public function termDocumentValueObject(TermDocument $termDocument)
    {
        $returnVal = array();
        switch ($termDocument->getType()) {
            case TermDocument::TYPE_SPECIALIZATION:
                $documentObj = $this->treatmentBundleService->getSpecialization($termDocument->getDocumentId());
                $returnVal = array(
                    'name' => $documentObj->getName(),
                    'url' => $this->router->generate('admin_specialization_edit', array('id' => $documentObj->getId()))
                );
                break;
            case TermDocument::TYPE_SUBSPECIALIZATION:
                $documentObj = $this->treatmentBundleService->getSubSpecialization($termDocument->getDocumentId());
                $returnVal = array(
                    'name' => $documentObj->getName(),
                    'url' => $this->router->generate('admin_subSpecialization_edit', array('id' => $documentObj->getId()))
                );
                break;
            case TermDocument::TYPE_TREATMENT:
                $documentObj = $this->treatmentBundleService->getTreatment($termDocument->getDocumentId());
                $returnVal = array(
                    'name' => $documentObj->getName(),
                    'url' => $this->router->generate('admin_treatment_edit', array('id' => $documentObj->getId()))
                );
                break;
        }
        
        return $returnVal;
    }
    
    public function getName()
    {
        return 'termTwigExtension';
    }
}