<?php

namespace HealthCareAbroad\TermBundle\Twig;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

class TermTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'get_document_type_constant' => new \Twig_Function_Method($this, 'getDocumentTypeConstant')
        );
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
    
    public function getName()
    {
        return 'termTwigExtension';
    }
}