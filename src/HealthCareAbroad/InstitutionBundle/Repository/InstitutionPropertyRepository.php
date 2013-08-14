<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Doctrine\ORM\Query;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionPropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionPropertyRepository extends EntityRepository
{
    /**
     * Get all anicilliary services
     */
    public function getAllServicesByInstitution($institution, $hydrationMode=Query::HYDRATE_OBJECT)
    {
        $institutionId = $institution;
        if ($institution instanceof Institution){
            $institutionId = $institution->getId();
        }
        $connection = $this->getEntityManager()->getConnection();
        $query = "SELECT a.*, b.* FROM institution_properties a LEFT JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id AND b.status = 1 AND a.institution_property_type_id = :propertyType";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('id', $institutionId);
        $stmt->bindValue('propertyType', InstitutionPropertyType::ANCILLIARY_SERVICE_ID);
        $stmt->execute();
        
        return $stmt->fetchAll($hydrationMode);
        
    }
    
    /**
     * Get available institution ancillary services that is still not assigned to institutionMedicalCenter
     */
    
    public function getUnAssignedInstitutionServicesToInstitutionMedicalCenter(Institution $institution, $assignedServices)
    {
        $ancillaryServicePropertyType = $this->getEntityManager()->getRepository('InstitutionBundle:InstitutionPropertyType')->findOneBy(array('name' => InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE));
        
        $ids = array();
        foreach ($assignedServices as $each) {
            $ids[] = $each->getId();
        }
        
        $idsNotIn = "'".\implode("', '",$ids)."'";
        $connection = $this->getEntityManager()->getConnection();
        
        /**
         * 
         * @TODO Verify Correct query
         */
        $query = "SELECT a.* ,b.* FROM institution_properties a LEFT JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id AND b.id NOT IN ({$idsNotIn})";
        //$query = "SELECT * FROM institution_properties a RIGHT JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id AND a.institution_property_type_id = :propertyType AND b.id NOT IN ({$idsNotIn})";
//         echo $query;exit;
        
        $stmt = $connection->prepare($query);
        $stmt->bindValue('id', $institution->getId());
        //$stmt->bindValue('propertyType', $ancillaryServicePropertyType->getId());
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getAvailableGlobalAwardsOfInstitution(Institution $institution, QueryOptionBag $options)
    {
        $globalAwardPropertyType = $this->getEntityManager()->getRepository('InstitutionBundle:InstitutionPropertyType')->findOneBy(array('name' => InstitutionPropertyType::TYPE_GLOBAL_AWARD));
        $sql = "SELECT a.value  FROM institution_properties a WHERE a.institution_property_type_id = :propertyType AND a.institution_id = :institutionId";
        
        $statement = $this->getEntityManager()
        ->getConnection()->prepare($sql);
        
        $statement->execute(array('propertyType' => $globalAwardPropertyType->getId(), 'institutionId' => $institution->getId()));
        
        $result = array();
        $ids = array();
        if($statement->rowCount() > 0) {
            while ($row = $statement->fetch(Query::HYDRATE_ARRAY)) {
                $ids[] = $row['value'];
            }
        }
            
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a,b')
            ->from('HelperBundle:GlobalAward', 'a')
            ->innerJoin('a.awardingBody', 'b')
            ->where('a.status = :globalAwardActiveStatus' )
            ->setParameter('globalAwardActiveStatus',GlobalAward::STATUS_ACTIVE)
            ->orderBy('a.name', 'ASC');
        
        if ($options->has('globalAward.name')) {
            $qb->andWhere('a.name LIKE :globalAwardName')
                ->setParameter('globalAwardName', '%'.$options->get('globalAward.name').'%');
        }
        
        if ($options->has('globalAward.type')) {
            $qb->andWhere('a.type = :globalAwardType')
                ->setParameter('globalAwardType', $options->get('globalAward.type'));
        }
        
        if (\count($ids)) {
            $qb->andWhere($qb->expr()->notIn('a.id', ':globalAwardIds'))
                ->setParameter('globalAwardIds', $ids);
        }
        //echo $qb->getQuery()->getSQL(); exit;
        return $qb->getQuery()->getResult();
    }
}