<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\StatisticsBundle\Services;

use HealthCareAbroad\StatisticsBundle\Exception\TrackerException;

use HealthCareAbroad\StatisticsBundle\Services\Trackers\Tracker;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Factory class for statistics daily trackers
 * 
 * @author Allejo Chris G. Velarde
 */
class TrackerFactory
{
    /**
     * @var array Tracker
     */
    private $trackers = array();
    
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * Get statistics daily tracker by statistics type
     * 
     * @param int $type
     * @return Tracker
     */
    public function getTrackerByType($type)
    {
        $this->_initializeTypes();

        if (!isset($this->trackers[$type])) {
            throw TrackerException::unknownTrackerType($type);
        }
        
        return $this->trackers[$type];
    }

    /**
     * Get statistics daily tracker by page route
     *
     * @param string $route
     * @return Tracker
     */
    public function getTrackerByRoute($route)
    {
        $type = StatisticTypes::getTypeByRoute($route);

        if(!$type) {
            return;
        }

        return $this->getTrackerByType($type);
    }

    private function _initializeTypes()
    {
        static $hasInitialized = false;
        
        if (!$hasInitialized) {
            
            foreach (StatisticTypes::getTrackerClasses() as $_type => $_class) {
                
                if (!\class_exists($_class)) {
                    throw TrackerException::trackerClassDoesNotExist($_class);
                }
                
                $_tracker = new $_class;
                if (!$_tracker instanceof Tracker) {
                    throw TrackerException::invalidTrackerClass($_tracker);
                }
                // inject dependencies
                $_tracker->setDoctrine($this->doctrine);
                
                $this->trackers[$_type] = $_tracker;
            } 
            
            $hasInitialized = true;
        }
    }    
}