<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class InstitutionService
{
    protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
    /**
     * Get the current institution set in the session
     * 
     */
    public function getCurrentInstitution()
    {
        $institutionId = 1;
        return $this->doctrine->getRepository('InstitutionBundle:Institution')->find($institutionId);
    }

    public function getInstitutions()
    {
        return $this->doctrine->getEntityManager()->createQueryBuilder()->add('select', 'p')->add('from', 'InstitutionBundle:Institution p')->add('where', 'p.status=1');
    }
    
    
    public function createInstitution($name, $description, $slug)
    {
    	$institution = new Institution();
		$institution->setName($name);
		$institution->setDescription($description);
		$institution->setSlug($slug);
		$institution->setStatus(1);
		
		$em = $this->doctrine->getEntityManager();
		$em->persist($institution);
		$em->flush();
		
		return $institution;
		
    }
    
    public function updateInstitutionMedicalCenters($institutionId, $newMedicalCenterIds = array(), $medicalCenterIdsWithProcedureType = array()) 
    {
		$conn = $this->doctrine->getConnection();

		// DELETE ALL institution_medical_centers without procedure types
		$deleteQry = "DELETE FROM institution_medical_centers WHERE institution_id = $institutionId AND medical_center_id NOT IN(".implode(',', $medicalCenterIdsWithProcedureType).")";
		$conn->exec($deleteQry);


		// ADD new institution_midical_centers
		if(!empty($newMedicalCenterIds)) {
			foreach($newMedicalCenterIds as $centerId) {
				$values[] = "($institutionId, $centerId)";
			}

			$addQry = "INSERT INTO institution_medical_centers(institution_id, medical_center_id) VALUES" . implode(',', $values);
			$conn->exec($addQry);
		}    	
    }
}