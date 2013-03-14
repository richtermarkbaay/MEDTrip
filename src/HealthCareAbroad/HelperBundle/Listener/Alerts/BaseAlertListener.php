<?php
/**
 * This is the base class for log Alert listener
 *
 * @author Adelbert Silla 
 */

namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class BaseAlertListener
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ContainerInterface
     */
    protected $container;
    
    protected $alertService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->doctrine = $this->container->get('doctrine');

        //$this->alertService = $this->container->get('services.alert');
    }
    
//     abstract function setOnAddAlert();

//     abstract function setOnRemoveAlert();
}