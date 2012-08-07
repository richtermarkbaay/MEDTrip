<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionMedicalCenterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionMedicalCenterRepository extends EntityRepository
{
	function getProcedureTypeIdsWithProcedure($medicalCenterId)
	{
		$conn = $this->_em->getConnection();
		$qry = "SELECT a.medical_procedure_type_id FROM institution_medical_procedure_types AS a " .
				"JOIN medical_procedures AS b ON a.medical_procedure_type_id = b.medical_procedure_type_id " .
				"JOIN institution_medical_procedures AS c ON b.id = c.medical_procedure_id ".
				"WHERE institution_medical_center_id = $medicalCenterId";

		$result = $conn->executeQuery($qry)->fetchAll();

		$ids = array();
		foreach($result as $each) {
			$ids[] = (int)$each['medical_procedure_type_id'];
		}

		return $ids;
	}
}