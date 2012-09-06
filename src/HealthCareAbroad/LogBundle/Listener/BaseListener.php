<?php
/**
 * Base class for common log listeners
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\LogBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class BaseListener
{
    /**
     * @var Registry
     */
    protected $doctrine;
    
    protected $logRepository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    abstract public function onAddAction();
    
    abstract public function onEditAction();
    
    abstract public function onDeleteAction();
    
    protected function saveLog()
    {
        //TODO: implement this
    }
} 