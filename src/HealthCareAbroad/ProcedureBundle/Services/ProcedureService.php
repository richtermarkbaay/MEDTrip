<?php

namespace HealthCareAbroad\ProcedureBundle\Services;

class ProcedureService
{
	protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }

    public function getActiveProcedures()
    {
    	return $this->doctrine->getEntityManager()->createQueryBuilder()
    			->add('select', 'p')
    			->add('from', 'ProcedureBundle:MedicalProcedure p')
    			->add('where', 'p.status = 1');
    }
    
}