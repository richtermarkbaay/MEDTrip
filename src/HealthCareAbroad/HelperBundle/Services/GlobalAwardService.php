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
    
    public function findById($id, $loadEager=true)
    {
        $query = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('g, ga')
            ->from('HelperBundle:GlobalAward', 'g')
            ->innerJoin('g.awardingBody', 'ga')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        
        return $query->getResult();
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
            $_arr = array('id' => $_award->getId(), 'label' => $_award->getName(), 'value' => $_award->getId());
            $_arr['awardingBody'] = $_award->getAwardingBody()->getName();
            $autocompleteSource[\strtolower($awardTypes[$_award->getType()])][] = $_arr;
        }
        
        return $autocompleteSource;
    }
    
    static public function groupGlobalAwardPropertiesByType(array $properties)
    {
        $awardTypes = GlobalAwardTypes::getTypes();
        $globalAwards = \array_flip(GlobalAwardTypes::getTypeKeys());
        
        // initialize holder for awards
        foreach ($globalAwards as $k => $v) {
            $globalAwards[$k] = array();
        }
        
        foreach ($properties as $_property) {
            $_globalAward = $_property->getValueObject();
            $globalAwards[\strtolower($awardTypes[$_globalAward->getType()])][] = $_property;
        }
        
        return $globalAwards;
    }
    
    public function findAwardsByIds(array $ids)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $query = $qb->select('a')
        ->from('HelperBundle:GlobalAward', 'a')
        ->where($qb->expr()->in('a.id', ':ids'))
        ->setParameter('ids', $ids)->getQuery();
    
        return $query->getResult();
    }
}