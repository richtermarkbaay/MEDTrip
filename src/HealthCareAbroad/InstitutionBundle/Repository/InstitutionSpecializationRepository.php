<?php
namespace HealthCareAbroad\InstitutionBundle\Repository;


use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use Proxies\__CG__\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionSpecializationRepository
 *
 * This class was generated by Harold Modesto. Add your own custom
 * repository methods below.
 */
class InstitutionSpecializationRepository extends EntityRepository
{
    public function getCountByMedicalCenterId($medicalCenterId) {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('count(a)')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->andWhere('a.medicalCenter = :medicalCenterId')
        ->setParameter('medicalCenterId', $medicalCenterId);

        $count = (int)$qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    public function getAvailableTreatments(InstitutionSpecialization $institutionSpecialization)
    {
        $qb = $this->_em->createQueryBuilder();

        $sql = "SELECT i.`treatment_id` as treatment_id FROM `institution_treatments` i WHERE i.`institution_specialization_id` = :institutionSpecializationId ";
        $statement = $this->getEntityManager()
            ->getConnection()->prepare($sql);

        $statement->execute(array('institutionSpecializationId' => $institutionSpecialization->getId()));
        $result = array();

        $ids = array();
        while ($row = $statement->fetch(Query::HYDRATE_ARRAY)) {
            $ids[] = $row['treatment_id'];
        }
        $hasIds = $statement->rowCount() > 0;
        if ($hasIds) {
            $dql = "SELECT t, s FROM TreatmentBundle:Treatment t LEFT JOIN t.subSpecializations s WHERE ".
                " t.id NOT IN (?1) ".
                "AND t.specialization = :specializationId ";

            $query = $this->_em->createQuery($dql)
                ->setParameter(1, $ids)
                ->setParameter('specializationId', $institutionSpecialization->getSpecialization()->getId());
        }
        else {
            $dql = "SELECT t, s FROM TreatmentBundle:Treatment t LEFT JOIN t.subSpecializations s WHERE t.specialization = :specializationId";

            $query = $this->_em->createQuery($dql)
                ->setParameter('specializationId', $institutionSpecialization->getSpecialization()->getId());
        }

        return $query->getResult();
    }

    public function getMedicalCentersList($institutionId)
    {
        $qb = $this->_em->createQueryBuilder()
        ->select('b.id, b.name')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->leftJoin('a.medicalCenter', 'b')
        ->add('where','a.institution = :institution')
        ->setParameter('institution', $institutionId)
        ->orderBy('b.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getByInstitutionMedicalCenter($institutionMedicalCenter)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a','b', 'c')
           ->from('InstitutionBundle:InstitutionSpecialization', 'a')
           ->leftJoin('a.specialization', 'b')
           ->leftJoin('a.treatments', 'c')
           ->where('a.institutionMedicalCenter = :institutionMedicalCenter')
           ->andWhere('a.status = :status')
           ->setParameter('institutionMedicalCenter', $institutionMedicalCenter)
           ->setParameter('status', InstitutionSpecialization::STATUS_ACTIVE);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getTreatmentCountByTreatmentId($treatmentId) {
        $qry = "SELECT count(*) FROM institution_treatments WHERE treatment_id = :treatmentId";
        $param = array('treatmentId' => $treatmentId);
        $count = $this->_em->getConnection()->executeQuery($qry, $param)->fetchColumn(0);

        return $count;
    }

    public function updateTreatments($institutionSpecializationId, $treatmentIds = array(), $deleletedTreatmentIds = array())
    {
        $conn = $this->_em->getConnection();

        if(count($treatmentIds)){
            $valuesHolder = "";
            $params = array("instSpecId" => $institutionSpecializationId);
            foreach($treatmentIds as $i => $treatmentId) {
                $valuesHolder .= ",(:instSpecId, :treatment_$i)";
                $params["treatment_$i"] = $treatmentId;
            }

            $qry = "INSERT INTO institution_treatments(institution_specialization_id, treatment_id) " .
                   "VALUES ". substr($valuesHolder, 1) .
                   "ON DUPLICATE KEY UPDATE treatment_id = treatment_id";
            $result = $conn->executeQuery($qry, $params);
        }

        if(count($deleletedTreatmentIds)) {
            // TODO - bind $deletedSpecializationId
            $deleteQry = "DELETE FROM institution_treatments " .
                         "WHERE institution_specialization_id = :institutionSpecializationId " .
                         "AND treatment_id IN (" . implode(',', $deleletedTreatmentIds) . ")";

            $deleteParams = array('institutionSpecializationId' => $institutionSpecializationId);

            $result = $conn->executeQuery($deleteQry, $deleteParams);
        }

        return $result;
    }

    public function getActiveSpecializations($institution)
    {
//           $qb1 = $this->createQueryBuilder('a');

                $qb = $this->_em->createQueryBuilder();
                $qb->select('b')
                    ->from('InstitutionBundle:InstitutionSpecialization', 'b')
                    ->leftJoin('b.institutionMedicalCenter', 'c')
                    ->leftJoin('b.specialization', 'd')
                    ->where('c.institution = :institution')
                    ->setParameter('institution', $institution)
                    ->groupBy('b.specialization');

                return $qb->getQuery()->getResult();

//         if (false === is_null($limit))
//             $qb->setMaxResults($limit);

//         return $qb->getQuery()
//         ->getResult();

    }


//    TODO - Current data structure cannot support this function already!
//     public function getMedicalCentersByTreatment(Treatment $procedureType, MedicalProcedure $procedure = null)
//     {
//         $qb = $this->_em->createQueryBuilder()
//         ->select('a')
//         ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
//         ->leftJoin('a.medicalCenter', 'b')
//         ->leftJoin('b.treatments', 'c');

//         if ($procedure) {
//             $qb->leftJoin('c.treatmentProcedures', 'd')
//             ->where('d = :procedure')
//             ->setParameter('procedure', $procedure);
//         }
//         else {
//             $qb->where('c = :procedureType')
//             ->setParameter('procedureType', $procedureType);
//         }

//         $qb->orderBy('b.name', 'ASC');

//         return $qb->getQuery()->getResult();
//     }


//    TODO - Current data structure cannot support this function already!
//     public function getMedicalCentersByCountry(Country $country)
//     {
//         $qb = $this->_em->createQueryBuilder()
//         ->select('a')
//         ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
//         ->leftJoin('a.institution', 'b')
//         ->leftJoin('a.medicalCenter', 'c')
//         ->where('b.country = :countryId')
//         ->andWhere('a.status = :status')
//         ->setParameter('countryId', $country->getId())
//         ->setParameter('status', InstitutionMedicalCenterGroupStatus::APPROVED)
//         ->orderBy('c.name', 'ASC');

//         return $qb->getQuery()->getResult();
//     }

//    TODO - Current data structure cannot support this function already!
//     public function getMedicalCentersByCity(City $country)
//     {
//         $qb = $this->_em->createQueryBuilder()
//         ->select('a')
//         ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
//         ->leftJoin('a.institution', 'b')
//         ->leftJoin('a.medicalCenter', 'c')
//         ->where('b.country = :countryId')
//         ->andWhere('a.status = :status')
//         ->setParameter('countryId', $country->getId())
//         ->setParameter('status', InstitutionMedicalCenterGroupStatus::APPROVED)
//         ->orderBy('c.name', 'ASC');

//         return $qb->getQuery()->getResult();
//     }

    public function getSpecializationTopDestinations($specialization, $numOfDestinations = 5)
    {
        $connection = $this->getEntityManager()->getConnection();

        if (is_object($specialization)) {
            $specialization = $specialization->getId();
        }

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS country, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN countries b ON a.country_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN specializations e ON d.specialization_id = e.id
            WHERE e.id = :specialization
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations
       ');
        $stmt->bindValue('specialization', $specialization, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();
        $topCountries = $stmt->fetchAll();

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS city, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN cities b ON a.city_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN specializations e ON d.specialization_id = e.id
            WHERE e.id = :specialization
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations
       ');

        $stmt->bindValue('specialization', $specialization, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();

        $topCities = $stmt->fetchAll();

        return array($topCountries, $topCities);
    }

    public function getSubSpecializationTopDestinations($subSpecialization, $numOfDestinations = 5)
    {
        $connection = $this->getEntityManager()->getConnection();

        if (is_object($subSpecialization)) {
            $subSpecialization = $subSpecialization->getId();
        }

        $stmt = $connection->prepare("
            SELECT b.id, b.name AS country, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN countries b ON a.country_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN specializations e ON d.specialization_id = e.id
            LEFT JOIN sub_specializations f ON e.id = f.specialization_id
            WHERE f.id = :subSpecialization
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations

       ");
        $stmt->bindValue('subSpecialization', $subSpecialization, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();
        $topCountries = $stmt->fetchAll();

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS city, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN cities b ON a.city_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN specializations e ON d.specialization_id = e.id
            LEFT JOIN sub_specializations f ON e.id = f.specialization_id
            WHERE f.id = :subSpecialization
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations
       ');
        $stmt->bindValue('subSpecialization', $subSpecialization, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();

        $topCities = $stmt->fetchAll();

        return array($topCountries, $topCities);
    }

    public function getTreatmentTopDestinations($treatment, $numOfDestinations = 5)
    {
        $connection = $this->getEntityManager()->getConnection();

        if (is_object($treatment)) {
            $treatment = $treatment->getId();
        }

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS country, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN countries b ON a.country_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN institution_treatments e ON d.id = e.institution_specialization_id
            LEFT JOIN treatments f ON e.treatment_id = f.id
            WHERE f.id = :treatment
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations
       ');
        $stmt->bindValue('treatment', $treatment, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();
        $topCountries = $stmt->fetchAll();

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS city, COUNT(*) AS institution_count
            FROM institutions a
            LEFT JOIN cities b ON a.city_id = b.id
            LEFT JOIN institution_medical_centers c ON a.id = c.institution_id
            LEFT JOIN institution_specializations d ON c.id = d.institution_medical_center_id
            LEFT JOIN institution_treatments e ON d.id = e.institution_specialization_id
            LEFT JOIN treatments f ON e.treatment_id = f.id
            WHERE f.id = :treatment
            GROUP BY b.id
            ORDER BY institution_count DESC
            LIMIT :numOfDestinations
       ');
        $stmt->bindValue('treatment', $treatment, \PDO::PARAM_INT);
        $stmt->bindValue('numOfDestinations', $numOfDestinations, \PDO::PARAM_INT);
        $stmt->execute();

        $topCities = $stmt->fetchAll();

        return array($topCountries, $topCities);
    }

    /**
     * Get the top specializations and treatments for specified country
     *
     * @param unknown $country
     * @param number $max
     * @return multitype:unknown
     */
    public function getCountryTopTreatments($country, $max = 5)
    {
        $connection = $this->getEntityManager()->getConnection();

        if (is_object($country)) {
            $country = $country->getId();
        }

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS specialization, COUNT(*) AS count
            FROM institution_specializations a
            LEFT JOIN specializations b ON a.specialization_id = b.id
            LEFT JOIN institution_medical_centers c ON a.institution_medical_center_id = c.id
            LEFT JOIN institutions AS d ON c.institution_id = d.id
            WHERE d.country_id = :country
            GROUP BY b.id
            ORDER BY count DESC
            LIMIT :max
       ');
        $stmt->bindValue('country', $country, \PDO::PARAM_INT);
        $stmt->bindValue('max', $max, \PDO::PARAM_INT);
        $stmt->execute();
        $topSpecializations = $stmt->fetchAll();

        $stmt = $connection->prepare('
            SELECT c.id, c.name AS treatment, COUNT(*) AS count
            FROM institution_specializations a
            LEFT JOIN institution_treatments b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments c ON b.treatment_id = c.id
            LEFT JOIN institution_medical_centers d ON a.institution_medical_center_id = d.id
            LEFT JOIN institutions AS e ON d.institution_id = e.id
            WHERE e.country_id = :country
            GROUP BY c.id
            ORDER BY count DESC
            LIMIT :max
       ');
        $stmt->bindValue('country', $country, \PDO::PARAM_INT);
        $stmt->bindValue('max', $max, \PDO::PARAM_INT);
        $stmt->execute();

        $topTreatments = $stmt->fetchAll();

        return array($topSpecializations, $topTreatments);
    }

    /**
     * Get the top specializations and treatments for specified city
     *
     * @param unknown $country
     * @param number $max
     * @return multitype:unknown
     */
    public function getCityTopTreatments($city, $max = 5)
    {
        $connection = $this->getEntityManager()->getConnection();

        if (is_object($city)) {
            $city = $city->getId();
        }

        $stmt = $connection->prepare('
            SELECT b.id, b.name AS specialization, COUNT(*) AS count
            FROM institution_specializations a
            LEFT JOIN specializations b ON a.specialization_id = b.id
            LEFT JOIN institution_medical_centers c ON a.institution_medical_center_id = c.id
            LEFT JOIN institutions AS d ON c.institution_id = d.id
            WHERE d.city_id = :city
            GROUP BY b.id
            ORDER BY count DESC
            LIMIT :max
       ');
        $stmt->bindValue('city', $city, \PDO::PARAM_INT);
        $stmt->bindValue('max', $max, \PDO::PARAM_INT);
        $stmt->execute();
        $topSpecializations = $stmt->fetchAll();

        $stmt = $connection->prepare('
            SELECT c.id, c.name AS treatment, COUNT(*) AS count
            FROM institution_specializations a
            LEFT JOIN institution_treatments b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments c ON b.treatment_id = c.id
            LEFT JOIN institution_medical_centers d ON a.institution_medical_center_id = d.id
            LEFT JOIN institutions AS e ON d.institution_id = e.id
            WHERE e.city_id = :city
            GROUP BY c.id
            ORDER BY count DESC
            LIMIT :max
       ');
        $stmt->bindValue('city', $city, \PDO::PARAM_INT);
        $stmt->bindValue('max', $max, \PDO::PARAM_INT);
        $stmt->execute();

        $topTreatments = $stmt->fetchAll();

        return array($topSpecializations, $topTreatments);
    }
}