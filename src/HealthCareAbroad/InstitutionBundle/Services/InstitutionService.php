<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\UserBundle\Services\InstitutionUserService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
class InstitutionService
{
	
    protected $doctrine;
    
    /**
     * @var HealthCareAbroad\UserBundle\Services\InstitutionUserService
     */
    protected $institutionUserService;
    
    public function setInstitutionUserService(InstitutionUserService $institutionUserService)
    {
        $this->institutionUserService = $institutionUserService;
    }
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
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
    
    public function updateInstitution(Institution $institution)
    {
    	$em = $this->doctrine->getEntityManager();
    	$em->persist($institution);
    	$em->flush();
    	
    	// failed to save
    	if (!$institution) {
    		return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
    	}
    	
    	return $institution;
    }
    
    public function createInstitution(Institution $institution)
    {
		$em = $this->doctrine->getEntityManager();
		$em->persist($institution);
		$em->flush();
		
		// failed to save
		if (!$institution) {
			return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
		}
		
		return $institution;
		
    }
    
    /*
     * TODO - This is already DEPRECATED, should be removed in time.
    */
    public function updateInstitutionMedicalCenters($institutionId, $newMedicalCenterIds = array(), $medicalCenterIdsWithProcedureType = array()) 
    {
		$conn = $this->doctrine->getConnection();

		// DELETE ALL institution_medical_centers without procedure types
		$deleteQry = "DELETE FROM institution_medical_centers WHERE ".
					 "institution_id = $institutionId";

		if(count($medicalCenterIdsWithProcedureType)) {
			$deleteQry .= " AND medical_center_id NOT IN(".implode(',', $medicalCenterIdsWithProcedureType).")";	
		}

		$conn->exec($deleteQry);


		// ADD new institution_midical_centers
		if(count($newMedicalCenterIds)) {
			foreach($newMedicalCenterIds as $centerId) {
				$values[] = "($institutionId, $centerId)";
			}

			$addQry = "INSERT INTO institution_medical_centers(institution_id, medical_center_id) VALUES" . implode(',', $values);
			$conn->exec($addQry);
		}    	
    }

    /*
     * TODO - This is already DEPRECATED, should be removed in time.
     */
    public function updateInstitutionProcedureTypes($institutionMedicalCenterId, $newProcedureTypeIds = array(), $procedureTypeIdsWithProcedure = array())
    {
    	$conn = $this->doctrine->getConnection();

    	// DELETE ALL institution_medical_procedure_types without institution procedures
    	$deleteQry = "DELETE FROM institution_medical_procedure_types ". 
    				 "WHERE institution_medical_center_id = $institutionMedicalCenterId";
    	
    	if(count($procedureTypeIdsWithProcedure)) {
    		$deleteQry .= " AND medical_procedure_type_id NOT IN(".implode(',', $procedureTypeIdsWithProcedure).")";
    	}

    	$conn->exec($deleteQry);
    
    	// ADD new institution_medical_procedure_types
    	if(count($newProcedureTypeIds)) {

    		foreach($newProcedureTypeIds as $procedureTypeId) {
    			$values[] = "($institutionMedicalCenterId, $procedureTypeId)";
    		}

    		$addQry = "INSERT INTO institution_medical_procedure_types(institution_medical_center_id, medical_procedure_type_id) VALUES" . implode(',', $values);
    		$conn->exec($addQry);
    	}
    }
    
    public function getAllStaffOfInstitution(Institution $institution)
    {
        $users = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findByInstitution($institution);
        
        $returnValue = array();
        foreach($users as $user) {
            $returnValue[] = $this->institutionUserService->getAccountData($user);
        }
        return $returnValue;
    }
    
    public function getStatusFilterOptions()
    {
    	return array(
    		'all' => 'All',
			Institution::ACTIVE => 'Active',
			Institution::INACTIVE => 'Inactive',
			Institution::APPROVED => 'Approved',
			Institution::UNAPPROVED => 'Unapproved',
    		Institution::SUSPENDED => 'Suspended'
    	);
    }

    public function getUpdateStatusOptions()
    {
		return array(
			'Activate' => Institution::ACTIVE,
			'Approve' => Institution::APPROVED,
			'Suspend' => Institution::SUSPENDED
		);
    }
}