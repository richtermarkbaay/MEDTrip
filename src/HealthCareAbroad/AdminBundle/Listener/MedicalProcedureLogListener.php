<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateTreatmentProcedureEvent;

class MedicalProcedureLogListener
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onDelete(CreateTreatmentProcedureEvent $event){

    }

    public function onEdit(CreateTreatmentProcedureEvent $event){
         
    }

    public function onAdd(CreateTreatmentProcedureEvent $event)
    {
         
    }
     

}