<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\InstitutionBundle;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\MediaBundle\Entity\Media;

use HealthCareAbroad\MediaBundle\Entity\Gallery;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for InstitutionMedicalCenter. Accessible by services.institution_medical_center service id
 *
 * @author Allejo Chris G. Velarde
 */
class InstitutionMedicalCenterService
{
    /**
     * @var Registry
     */
    private $doctrine;

    static private $institutionMedicalCenter;

    /**
     * @var InstitutionMedicalCenterPropertyService
     */
    private $institutionMedicalCenterPropertyService;

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFullInstitutionMedicalCenterBySlug($slug = '')
    {
        if(!$slug) {
            return null;
        }
        // USING static flag will yield unexpected results when ran in test suites
        //static $isLoaded = false;

        //if(!$isLoaded) {
            $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f, g, h, i, j, k, l')->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->leftJoin('a.institution', 'b')
            ->leftJoin('b.country', 'c')
            ->leftJoin('b.city', 'd')
            ->leftJoin('a.institutionSpecializations', 'e')
            ->leftJoin('e.specialization', 'f')
            ->leftJoin('e.treatments', 'g')
            ->leftJoin('g.subSpecializations', 'h')
            ->leftJoin('a.media', 'i')
            ->leftJoin('a.logo', 'j')
            ->leftJoin('a.doctors', 'k')
            ->leftJoin('k.specializations', 'l')
            ->where('a.slug = :centerSlug')
            ->andWhere('a.status = :status')
            ->orderBy('f.id', 'ASC')
            ->setParameter('centerSlug', $slug)
            ->setParameter('status', InstitutionMedicalCenterStatus::APPROVED);
            $result = $qb->getQuery()->getOneOrNullResult();
            //self::$institutionMedicalCenter = $qb->getQuery()->getOneOrNullResult();

            //$isLoaded = true;
        //}

       // return self::$institutionMedicalCenter;
       return $result;
    }


    /**
     * Get active institution specializations of an institution medical center
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return array InstitutionSpecialization
     */
    public function getActiveSpecializations(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitutionMedicalCenter($institutionMedicalCenter);
    }


    /**
     * List active specializations of a medical center
     * Returns a flat array of specializationId => specializationName
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return array (specializationId => specializationName)
     */
    public function listActiveSpecializations(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $institutionSpecializations = $this->getActiveSpecializations($institutionMedicalCenter);
        $list = array();
        foreach ($institutionSpecializations as $_each) {
            $specialization = $_each->getSpecialization();
            $list[$specialization->getId()] = $specialization->getName();
        }

        
        return $list;
    }


    public function setInstitutionMedicalCenterPropertyService(InstitutionMedicalCenterPropertyService $service)
    {
        $this->institutionMedicalCenterPropertyService = $service;
    }

    /**
     * Get values of medical center $institutionMedicalCenter for property type $propertyType
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @return array InstitutionMedicalCenterProperty
     */
    public function getPropertyValues(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId";
        $result = $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->getResult();

        return $result;
    }

    /**
     * Check if $institutionMedicalCenter has a property type value of $value
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @param mixed $value
     */
    public function hasPropertyValue(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType, $value)
    {
        $result = $this->getPropertyValue($institutionMedicalCenter, $propertyType, $value);

        return !\is_null($result) ;
    }

    /**
     * Get InstitutionMedicalCenterProperty by institution medical center, institution propertype and the value
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @param mixed $value
     * @return InstitutionMedicalCenterProperty
     */
    public function getPropertyValue(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType, $value )
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId AND a.value = :value";
        $result = $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->setParameter('value', $value)
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * Delete the values for $propertyType of $institutionMedicalCenter
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     */
    public function clearPropertyValues(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType)
    {
        $dql = "DELETE FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId";
        $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->execute();
    }

    /**
     * Layer to Doctrine find by id. Apply caching here.
     *
     * @param int $id
     * @param mixed $eagerly, bool|array if array it must be a key, value format as "alias => property" of InstitutionMedicalCenter - ex: array('doctors' => 'a.doctors')
     * @return InstitutionMedicalCenter
     */
    public function findById($id, $eagerly = true)
    {
        if(is_array($eagerly)) {
            $qb = $this->doctrine->getEntityManagerForClass('InstitutionBundle:InstitutionMedicalCenter')->createQueryBuilder();
            $qb->select('a, ' . implode(', ', array_keys($eagerly)))
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
            foreach($eagerly as $alias => $property) {
                $qb->leftJoin($property, $alias);
            }

            $qb->where('a.id = :centerId')->setParameter('centerId', $id);
            
            $result = $qb->getQuery()->getOneOrNullResult();

            return $result;
        }

        if ($eagerly) {
            $qb = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getQueryBuilderForEagerlyLoadedMedicalCenter();
            $qb->andWhere('imc.id = :id')->setParameter('id', $id);

            $result = $qb->getQuery()->getOneOrNullResult();

        } else {
            $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($id);
        }
        
        return $result;
    }

    /**
     * Save InstitutionMedicalCenter to database
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return InstitutionMedicalCenter
     */
    public function save(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionMedicalCenter);
        $em->flush();

        return $institutionMedicalCenter;
    }

    public function setInstitutionStatusActive(Institution $institution)
    {
        $institution->setStatus(Institution::ACTIVE);
        $em = $this->doctrine->getEntityManager();
        $em->persist($institution);
        $em->flush();
    }
    /**
     * Save an InstitutionMedicalCenter as DRAFT
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function saveAsDraft(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::DRAFT);

        return $this->save($institutionMedicalCenter);
    }

    /**
     * Check if InstitutionMedicalCenter is of DRAFT status
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return boolean
     */
    public function isDraft(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $institutionMedicalCenter->getStatus() == InstitutionMedicalCenterStatus::DRAFT;
    }
    
    /**
     * Clear the related BusinessHour of medical center
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function clearBusinessHours(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        if($institutionMedicalCenter->getId()) {
            $this->doctrine->getRepository('InstitutionBundle:BusinessHour')
            ->deleteByInstitutionMedicalCenter($institutionMedicalCenter);            
        }
    }


    /**
     * Get ancillary services of a medical center
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return boolean
     */
    public function getMedicalCenterServices(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $ancilliaryServices = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($institutionMedicalCenter);

        return $ancilliaryServices;
    }

    /**
     * Get global awards of an institution medical center
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return array GlobalAward
     */
    public function getMedicalCenterGlobalAwards(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllGlobalAwardsByInstitutionMedicalCenter($institutionMedicalCenter);
    }

    
    /**
     * @deprecated ?
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function getGroupedMedicalCenterGlobalAwards(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $awardTypes = GlobalAwardTypes::getTypes();
        $globalAwards = \array_flip(GlobalAwardTypes::getTypeKeys());

        // initialize holder for awards
        foreach ($globalAwards as $k => $v) {
            $globalAwards[$k] = array();
        }

        $imcProperties = $this->institutionMedicalCenterPropertyService->getGlobalAwardPropertiesByInstitutionMedicalCenter($institutionMedicalCenter);
        
        foreach ($imcProperties as $imcp_arr) {
            
            if(!empty($imcp_arr)) {
                foreach ($imcp_arr as $imcp ) {
                    $_globalAward = $imcp->getValueObject();
                    $globalAwards[\strtolower($awardTypes[$_globalAward->getType()])][] = $imcp;
                }
                
            }                        
            
        }
//         foreach ($this->getMedicalCenterGlobalAwards($institutionMedicalCenter) as $_globalAward) {
//             $globalAwards[\strtolower($awardTypes[$_globalAward->getType()])][] = array(
//                 'global_award' => $_globalAward,
//             );
//         }

        return $globalAwards;
    }

    public function getActiveMedicalCentersByInstitution(Institution $institution)
    {
         $result = $this->doctrine->getRepository('InstitutionBundle:Institution')->getActiveInstitutionMedicalCenters($institution);

         return $result;
    }
    
    public function getApprovedMedicalCenters()
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy(array('status' => InstitutionMedicalCenterStatus::APPROVED));
    
        return $result;
    }
    
    public function getApprovedMedicalCentersByFiltersAndInstitutionSearchName($params)
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getApprovedInstitutionMedicalCentersByFiltersAndInstitutionSearchName($params);
        
        return $result;
    }
    
    public function getAvailableTreatmentsByInstitutionSpecialization(InstitutionSpecialization $institutionSpecialization)
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getAvailableTreatments($institutionSpecialization);

        return $result;
    }

    /**
     * Check if specialist exist
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return array InstitutionMedicalCenterProperty
     */
    public function hasSpecialist(InstitutionMedicalCenter $institutionMedicalCenter, $doctor)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
        LEFT JOIN a.doctors b
        WHERE a.id = :institutionMedicalCenterId AND b.id = :doctorId";
        $result = $this->doctrine->getEntityManager()
        ->createQuery($dql)
        ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
        ->setParameter('doctorId', $doctor)
        ->getResult();

        return $result;
    }

    /**
    public function searchMedicaCenterWithSearchTerm(Institution $institution, $searchTerm)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                    LEFT JOIN a.institutionSpecializations b
                    LEFT JOIN b.treatments c
                    LEFT JOIN c.subSpecializations d
                    WHERE b.specialization = :searchTerm
                    OR c.id = :searchTerm
                    OR d.id = :searchTerm
                    OR a.status != :inactive";

        $dql = "SELECT imc, imcs, s, t FROM InstitutionBundle:InstitutionMedicalCenter imc
            INNER JOIN imc.institutionSpecializations imcs
            INNER JOIN imcs.specialization s
            LEFT JOIN imcs.treatments t
            LEFT JOIN t.subSpecializations ss
            WHERE imc.status != :inactive
            AND imc.institution = :institutionId
            AND (t.name LIKE :searchTerm OR ss.name LIKE :searchTerm OR s.name LIKE :searchTerm OR imc.name LIKE :searchTerm)";

        
        $query = $this->doctrine->getEntityManager()
        ->createQuery($dql)
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->setParameter('inactive', InstitutionMedicalCenter::STATUS_INACTIVE)
        ->setParameter('institutionId', $institution->getId());
        //echo $query->getSQL(); exit;
        //->getResult();

        return $query->getResult();
    }
    **/
    
    
    public function groupMedicalCentersByStatus($medicalCenters){
        $results = array(
            InstitutionMedicalCenterStatus::APPROVED => array(),
            InstitutionMedicalCenterStatus::DRAFT => array(),
            InstitutionMedicalCenterStatus::PENDING => array(),
            InstitutionMedicalCenterStatus::EXPIRED => array(),
            InstitutionMedicalCenterStatus::ARCHIVED => array()
        );

        foreach ($medicalCenters as $each){
            $results[$each->getStatus()][] = $each;
        }

        return $results;
    }
    
    public function getContactDetailsByInstitutionMedicalCenter(InstitutionMedicalCenter $center)
    {
        $connection = $this->doctrine->getEntityManager()->getConnection();
        $query = "SELECT * FROM contact_details a
        LEFT JOIN institution_medical_center_contact_details b ON a.id = b.contact_detail_id
        WHERE b.institution_medical_center_id = :imcId";
    
        $stmt = $connection->prepare($query);
        $stmt->bindValue('imcId', $center->getId());
        $stmt->execute();
    
        return $stmt->fetchAll();
    }

    static public function getFirstInstitutionSpecialization($institutionMedicalCenter)
    {
        $specialization = null;
        $institutionSpecializations = $institutionMedicalCenter->getInstitutionSpecializations();

        if(is_object($institutionSpecializations)) {
            $specialization = $institutionSpecializations->first();
        } else {
            $specialization = isset($institutionSpecialization[0]) ? $institutionSpecialization[0] : null;
        }

        return $specialization;
    }

    public function getCountByInstitution(Institution $institution)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getCountByInstitution($institution);
    }

    private static $defaultDailyValues = array(
        'isOpen' => 1,
        'notes' => '',
    );



    /**
     * Note: This doesn't add the media to institution gallery.
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param Media $media
     */
    function saveMediaAsLogo(InstitutionMedicalCenter $institutionMedicalCenter, Media $media)
    {
        $institutionMedicalCenter->setLogo($media);

        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionMedicalCenter);
        $em->flush($institutionMedicalCenter);
    }
    
    function updateStatus(InstitutionMedicalCenter $center, $status)
    {
        $center->setStatus($status);

        $em = $this->doctrine->getEntityManager();
        $em->persist($center);
        $em->flush();
    }

    public function addMedicalCenterSpecializationsWithTreatments(InstitutionMedicalCenter $institutionMedicalCenter, array $specializationsWithTreatments)
    {
        $specializationRepo = $this->doctrine->getRepository('TreatmentBundle:Specialization');

        //TODO: optimize this is very db intensive
       $institutionTreatmentIds = array();
       $specializationIds = array();
       $subQuery = '';
        foreach ($specializationsWithTreatments as $specializationId => $treatmentIds) {

            if(!isset($treatmentIds['treatments']) || empty($treatmentIds['treatments'])) {
                continue;
            }

            $specialization = $specializationRepo->find($specializationId);
            
            $subQuery .= "('".$specialization->getDescription()."', 1, ".$specialization->getId().", ".$institutionMedicalCenter->getId()."),";

            $institutionTreatmentIds[$specializationId] = $treatmentIds['treatments'];
            $specializationIds[] = $specializationId;
            
        }

        $conn = $this->doctrine->getEntityManager()->getConnection();
        $subQuery = substr($subQuery, 0 , -1) . " ON DUPLICATE KEY UPDATE status = 1";
        $sqlQuery = "INSERT INTO institution_specializations (description, status, specialization_id, institution_medical_center_id) VALUES $subQuery";
        $conn->executeQuery($sqlQuery);

        $sqlQuery ="SELECT id, specialization_id from institution_specializations where institution_medical_center_id = " . 
                $institutionMedicalCenter->getId() . " and specialization_id in ( " . implode(',', $specializationIds). ")";
        $result = $conn->executeQuery($sqlQuery);
        $subQuery = '';
        $resultIds =array();
    
        foreach($result as $each) {
            $treatmentIds = $institutionTreatmentIds[$each['specialization_id']];
                foreach ($treatmentIds as $treatmentId) {
                    $subQuery .= "(". $each['id'].", ". $treatmentId ."),";
                }
            $resultIds[] = $each['id'];
        }
        $subQuery = substr($subQuery, 0 , -1) . " ON DUPLICATE KEY UPDATE treatment_id = treatment_id";
        $sqlQuery = "INSERT INTO institution_treatments (institution_specialization_id, treatment_id) VALUES $subQuery";
        $conn->executeQuery($sqlQuery);
        $conn->close();
        

        return $resultIds;
    }
    
    
    public function extractDaysFromWeekdayBitValue($bitValue)
    {
        $weekdays = array(
            array('short' => 'Sun', 'long' => 'Sunday', 'day' => 0),
            array('short' => 'Mon', 'long' => 'Monday', 'day' => 1),
            array('short' => 'Tue', 'long' => 'Tuesday', 'day' => 2),
            array('short' => 'Wed', 'long' => 'Wednesday', 'day' => 3),
            array('short' => 'Thu', 'long' => 'Thursday', 'day' => 4),
            array('short' => 'Fri', 'long' => 'Friday', 'day' => 5),
            array('short' => 'Sat', 'long' => 'Saturday', 'day' => 6),
        );
        $days = array();
        foreach ($weekdays as $_dayAttr) {
            $dayBitValue = $this->getBitValueOfDay($_dayAttr['day']);
            // bit compare
            if ($dayBitValue & $bitValue) {
                $days[] = $_dayAttr;
            }
        }
        
        return $days;
    }
    
    public function getBitValueOfDay($day)
    {
        return \pow(2, $day);
    }
    
    public function getListOfEmptyFieldsOnInstitutionMedicalCenter(InstitutionMedicalCenter $center)
    {
        $emptyFields = array();
        if(!$center->getDescription()) {
            $emptyFields[] = 'description';
        }
    
        if(!$center->getLogo()) {
            $emptyFields[] = 'logo';
        }
    
        if(!$center->getContactDetails()->count()) {
            $emptyFields[] = 'contact details';
        }
    
        if(!$center->getSocialMediaSites()) {
            $emptyFields[] = 'social media sites';
        }
    
        if(!$center->getDoctors()) {
            $emptyFields[] = 'doctors';
        }
    
        return $emptyFields;
    }
    
    static public function institutionMedicalCenterToArray(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $data = array(
        	'id' => $institutionMedicalCenter->getId(),
            'name' => $institutionMedicalCenter->getName()
        );

        return $data;
    }
}