<?php
namespace HealthCareAbroad\LogBundle\Services;

use HealthCareAbroad\LogBundle\Exception\ListenerException;

use HealthCareAbroad\LogBundle\Entity\LogClass;

use HealthCareAbroad\LogBundle\Entity\Log;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Bundle\DoctrineBundle\Registry;

class LogService
{
    /**
     * @var Registry
     */
    protected $doctrine;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var array
     */
    private static $logClassMap = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $this->container->get('doctrine');
    }
    
    public function save(Log $log)
    {
        $em = $this->doctrine->getEntityManager('logger');
        $em->persist($log);
        $em->flush();
    }
    
    public function saveLogClass(LogClass $logClass)
    {
        $em = $this->doctrine->getEntityManager('logger');
        $em->persist($logClass);
        $em->flush();
    }
    
    /**
     * Find an instance of LogClass by class name. If class exists and there is no record yet, a new LogClass will be saved
     * 
     * @param string $className
     * @return \HealthCareAbroad\LogBundle\Entity\LogClass
     */
    public function getLogClassByName($className)
    {
        if (!\class_exists($className)) {
            throw ListenerException::logClassDoesNotExist($className);
        }
        
//         if (\array_key_exists($className, self::$logClassMap)) {
//             $logClass = self::$logClassMap[$className];
//         }
//         else {
//             $logClass = $this->doctrine->getRepository('LogBundle:LogClass')->findOneBy(array('name' => $className));
            
//             if (!$logClass) {
//                 $logClass = new LogClass();
//                 $logClass->setName($className);
//                 $this->saveLogClass($logClass);
//             }
//             else {
//                 self::$logClassMap[$className] = $logClass;
//             }
//         }

        $logClass = $this->doctrine->getEntityManager('logger')->getRepository('LogBundle:LogClass')->findOneBy(array('name' => $className));
        
        if (!$logClass) {
            $logClass = new LogClass();
            $logClass->setName($className);
            $this->saveLogClass($logClass);
        }
                
        return $logClass;
    }
}