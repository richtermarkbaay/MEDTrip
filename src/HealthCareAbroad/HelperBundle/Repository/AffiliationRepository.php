<?php

namespace HealthCareAbroad\HelperBundle\Repository;

use HealthCareAbroad\HelperBundle\Entity\Affiliation;

use Doctrine\ORM\EntityRepository;

/**
 * AffiliationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AffiliationRepository extends EntityRepository
{

	function getInstitutionAffiliations($medicalCenterId)
	{
		$conn = $this->_em->getConnection();
		$qry = "SELECT b.id,b.name FROM institution_affiliations AS a " .
						"JOIN affiliations AS b ON a.affiliation_id = b.id " .
						"JOIN  institution_medical_centers AS c ON a.institution_medical_center_id = c.id " .
						"WHERE a.institution_medical_center_id = $medicalCenterId and b.status = ". Affiliation::STATUS_ACTIVE ." ";
	
		$result = $conn->executeQuery($qry)->fetchAll();
	
		return $result;
	}
}