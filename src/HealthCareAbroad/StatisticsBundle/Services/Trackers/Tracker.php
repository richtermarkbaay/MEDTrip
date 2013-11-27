<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Base class for statistics daily tracker. 
 * We will not include instances of this class in dependency injection since this should be initiated only through TrackerFactory
 * 
 * @author Allejo Chris G. Velarde
 */
abstract class Tracker
{
    protected $data;
    
    /**
     * @var Registry
     */
    protected $doctrine;
    
    /**
     * Add statistics data from parameters
     * 
     * @param StatisticsParameterBag $parameters
     * @return StatisticsDaily
     */
    abstract public function createDataFromParameters(StatisticsParameterBag $parameters);
    
    /** 
     * @param Request $request
     * @return StatisticsDaily
     */
    abstract public function createDataFromHttpRequest(Request $request);
    
    /**
     * 
     * @param StatisticsDaily $data
     * @return boolean
     */
    abstract public function add(StatisticsDaily $data);
    
    final public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
        
        return $this;
    }
    
    final public function batchSave()
    {
        $em = $this->doctrine->getEntityManager('statistics');
        foreach ($this->data as $_each) {
            $em->persist($_each);
        }
        $em->flush();
    }
    
    final public function save(StatisticsDaily $data)
    {
        $em = $this->doctrine->getEntityManager('statistics');
        $em->persist($data);
        $em->flush();
    }
}