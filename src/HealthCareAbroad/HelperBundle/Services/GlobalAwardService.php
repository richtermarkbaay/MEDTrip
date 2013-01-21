<?php
namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use Doctrine\Bundle\DoctrineBundle\Registry;

class GlobalAwardService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    /**
     * Get all available awards and group it by type, used in autocomplete fields for global awards
     */
    public function getAutocompleteSource()
    {
        $globalAwards = $this->doctrine->getRepository('HelperBundle:GlobalAward')->findBy(array('status' => GlobalAward::STATUS_ACTIVE));
        $awardTypes = GlobalAwardTypes::getTypes();
        $autocompleteSource = \array_flip(GlobalAwardTypes::getTypeKeys());
        
        // initialize holder for awards
        foreach ($autocompleteSource as $k => $v) {
            $autocompleteSource[$k] = array();
        }
        
        foreach ($globalAwards as $_award) {
            $_arr = array('id' => $_award->getId(), 'label' => $_award->getName());
            $_arr['awardingBody'] = $_award->getAwardingBody()->getName();
            $autocompleteSource[\strtolower($awardTypes[$_award->getType()])][] = $_arr;
        }
        
        return $autocompleteSource;
    }    
}