<?php
/**
 * Service class for Institution
 *
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;

use Doctrine\ORM\Query;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\Validator\Constraints\DateTime;

use HealthCareAbroad\HelperBundle\Twig\TimeAgoExtension;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\MediaBundle\Entity\Media;

use HealthCareAbroad\MediaBundle\Entity\Gallery;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\UserBundle\Services\InstitutionUserService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HeathCareAbroad\HelperBundle\Twig\TimeAgoTwigExtension;
class InstitutionService
{
    protected $doctrine;
    protected $router;
    protected $timeAgoExt;
    /**
     * @var static Institution
     */
    protected static $institution;

    /**
     * @var HealthCareAbroad\UserBundle\Services\InstitutionUserService
     */
    protected $institutionUserService;

    /**
     * @var InstitutionPropertyService
     */
    protected $institutionPropertyService;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
        $this->doctrine = $doctrine;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    function setMediaTwigExtension($mediaTwigExtension)
    {
        $this->mediaTwigExtension = $mediaTwigExtension;
    }

    public function setTimeAgoExtension(\HealthCareAbroad\HelperBundle\Twig\TimeAgoTwigExtension $timeAgoExt)
    {
        $this->timeAgoExt = $timeAgoExt;
    }
    
    public function updatePayingClientStatus(Institution $institution, $payingClientStatus)
    {
        $qb = $this->doctrine->getEntityManagerForClass('InstitutionBundle:InstitutionMedicalCenter')->createQueryBuilder();
        $qb->select('a.payingClient')
           ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
           ->where('a.status = :status')
           ->andWhere('a.institution = :institutionId')
           ->addOrderBy('a.payingClient', 'DESC')
           ->setMaxResults(1)
           ->setParameter('status', InstitutionMedicalCenterStatus::APPROVED)
           ->setParameter('institutionId', $institution->getId());

        $newInstitutionPayingClient = (int)$qb->getQuery()->getSingleScalarResult();

        if((int)$institution->getPayingClient() != $newInstitutionPayingClient) {
            $institution->setPayingClient($newInstitutionPayingClient);
            $em = $this->doctrine->getEntityManagerForClass('InstitutionBundle:Institution');
            $em->persist($institution);
            $em->flush();
        }
    }

    /**
     * Count active medical centers of an institution
     *
     * @author acgvelarde
     * @param Institution $institution
     * @return int
     */
    public function countActiveMedicalCenters(Institution $institution)
    {
        return $this->doctrine->getRepository('InstitutionBundle:Institution')->countActiveInstitutionMedicalCenters($institution);
    }

    /**
     * List active Specializations of an institution.
     * Returns a flat array of specializationId => specializationName
     *
     * @param Institution $institution
     * @return array
     * @author acgvelarde
     */
    public function listActiveSpecializations(Institution $institution)
    {
        $institutionSpecializations = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getActiveSpecializationsByInstitution($institution);

        $list = array();
        foreach ($institutionSpecializations as $_each) {
            $specialization = $_each->getSpecialization();
            $list[$specialization->getId()] = $specialization->getName();
        }

        return $list;
    }

    public function getContactDetailsByInstitution(Institution $institution)
    {
        $connection = $this->doctrine->getEntityManager()->getConnection();
        $query = "SELECT * FROM contact_details a
                        LEFT JOIN institution_contact_details b ON a.id = b.contact_detail_id
                        WHERE b.institution_id = :institutionId";

        $stmt = $connection->prepare($query);
        $stmt->bindValue('institutionId', $institution->getId());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getFullInstitutionBySlug($slug = '')
    {
        if(!$slug) {
            return null;
        }
        // USING static flag will yield unexpected results when ran in test suites
        //static $isLoaded = false;

        //if(!$isLoaded) {
            $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, f, g, h')->from('InstitutionBundle:Institution', 'a')
            ->leftJoin('a.institutionMedicalCenters ', 'b', Join::WITH, 'b.status = :medicalCenterStatus')
            ->leftJoin('b.institutionSpecializations', 'c')
            ->leftJoin('c.specialization', 'd')
            ->leftJoin('a.country', 'f')
            ->leftJoin('a.city', 'g')
            ->leftJoin('a.logo', 'h')
            ->where('a.slug = :institutionSlug')
            ->andWhere('a.status = :status')
            ->setParameter('institutionSlug', $slug)
            ->setParameter('status', InstitutionStatus::getBitValueForApprovedStatus())
            ->setParameter('medicalCenterStatus', InstitutionMedicalCenterStatus::APPROVED);
            $result = $qb->getQuery()->getOneOrNullResult();
            //self::$institution = $qb->getQuery()->getOneOrNullResult();

            //$isLoaded = true;
       // }

        //return self::$institution;
        return $result;
    }

    public function getFullInstitutionById($id = null)
    {
        // USING static flag will yield unexpected results when ran in test suites
        //static $isLoaded = false;

        //if(!$isLoaded) {
            $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f, g')->from('InstitutionBundle:Institution', 'a')
            ->leftJoin('a.country', 'b')
            ->leftJoin('a.city', 'c')
            ->leftJoin('a.logo', 'd')
            ->leftJoin('a.featuredMedia', 'e')
            ->leftJoin('a.contactDetails', 'f')
            ->leftJoin('a.medicalProviderGroups', 'g')
            ->where('a.id = :id')
            ->setParameter('id', $id);
            $result = $qb->getQuery()->getOneOrNullResult();

            //self::$institution = $qb->getQuery()->getOneOrNullResult();

            //$isLoaded = true;
        //}

        return $result;
    }

    public function setInstitutionPropertyService(InstitutionPropertyService $v)
    {
        $this->institutionPropertyService = $v;
    }

    /**
     * Check if $institution is of type SINGLE_CENTER
     *
     * @param Mixed <Institution, array> $institution
     * @return boolean
     */
    public function isSingleCenter(Institution $institution)
    {
        if ($institution instanceof Institution) {
            $type = $institution->getType();
        }
        else {
            $type = $institution['type'];
        }
        
        return InstitutionTypes::SINGLE_CENTER == $type;
    }

    /**
     * Check if $institution is of type MULTIPLE_CENTER
     *
     * @param Mixed <Institution, array> $institution
     * @return boolean
     */
    static function isMultipleCenter($institution)
    {
        if ($institution instanceof Institution) {
            $type = $institution->getType();
        }
        else {
            $type = $institution['type'];
        }

        return InstitutionTypes::MULTIPLE_CENTER == $type;
    }

    /**
     * Get Institution Route Name
     *
     * @param Mixed <Institution, array> $institution
     * @return route name
     */
    static function getInstitutionRouteName($institution)
    {
        return self::isMultipleCenter($institution)
            ? 'frontend_institution_multipleCenter_profile'
            : 'frontend_institution_singleCenter_profile';
    }

    /**
     * Save Institution to database
     *
     * @param Institution $institution
     * @return Institution
     */
    public function save(Institution $institution)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institution);
        $em->flush();

        return $institution;
    }


    /**
     *
     * @param Institution $institution
     * @return boolean
     */
    public function isActive(Institution $institution)
    {
        $activeStatus = InstitutionStatus::getBitValueForActiveStatus();


        return $activeStatus == $activeStatus & $institution->getStatus() ? true : false;
    }

    /**
     * Check if the $institution is Approved
     *
     * @param Institution $institution
     * @return boolean
     * @author acgvelarde
     */
    public function isApproved(Institution $institution)
    {
        return $institution->getStatus() == InstitutionStatus::getBitValueForApprovedStatus();
    }

    public function setInstitutionUserService(InstitutionUserService $institutionUserService)
    {
        $this->institutionUserService = $institutionUserService;
    }

    public function getAdminUsers(Institution $institution)
    {
        $_users = $this->doctrine->getRepository('UserBundle:InstitutionUser')
            ->findByTypeName($institution, 'ADMIN');

        $retVal = array();
        foreach ($_users as $user) {
            try {
                $retVal[] = $this->institutionUserService->getAccountData($user);
            }
            catch (\Exception $e) {
                $retVal[] = $user;
            }

        }

        return $retVal;
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

    public function getAllNotExpiredArchivedAndInactiveMedicalCenters(Institution $institution)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a WHERE a.institution = :institutionId AND a.status != :inActive AND a.status != :expired AND a.status != :archived ";

        $query = $this->doctrine->getEntityManager()->createQuery($dql)
        ->setParameter('institutionId', $institution->getId())
        ->setParameter('inActive', InstitutionMedicalCenterStatus::INACTIVE)
        ->setParameter('expired', InstitutionMedicalCenterStatus::EXPIRED)
        ->setParameter('archived', InstitutionMedicalCenterStatus::ARCHIVED);
        return $query->getResult();
    }

    public function getActiveMedicalCenters(Institution $institution)
    {
        return $this->doctrine->getRepository('InstitutionBundle:Institution')->getActiveInstitutionMedicalCenters($institution);
    }

    public function getAllMedicalCenters(Institution $institution)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')
            ->findBy(array('institution' => $institution->getId()));
    }

    public function getRecentlyAddedMedicalCenters(Institution $institution, QueryOptionBag $options=null)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('i')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'i')
            ->where('i.institution = :institutionId')
            ->orderBy('i.dateCreated','desc')
            ->setParameter('institutionId', $institution->getId());

        if ($limit = $options->get(QueryOption::LIMIT, null)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the first added medical center of an institution
     *
     * @param Institution $institution
     * @return InstitutionMedicalCenter
     */
    public function getFirstMedicalCenter(Institution $institution)
    {
        return $this->getFirstMedicalCenterByInstitutionId($institution->getId());
    }

    /**
     * Get the first added medical center of an institution
     * @param int $insitutionId
     */
    public function getFirstMedicalCenterByInstitutionId($institutionId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getFirstByInstitutionId($institutionId);
    }

    /**
     * Get ancillary services of an institution
     *
     * @param Institution $institution
     * @return boolean
     */
    public function getInstitutionServices(Institution $institution)
    {
        $ancilliaryServices = $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')->getAllServicesByInstitution($institution);

        return $ancilliaryServices;



    }

    /**
     * Check if $institution has a property type value of $value
     *
     * @param Institution $institution
     * @param InstitutionPropertyType $propertyType
     * @param mixed $value
     * @return boolean
     * @deprecated added for BC
     */
    public function hasPropertyValue(Institution $institution, InstitutionPropertyType $propertyType, $value)
    {
        return $this->institutionPropertyService->hasPropertyValue($institution, $propertyType, $value);
    }

    /**
     * Get global awards of an institution
     *
     * @param Institution $institution
     * @return array GlobalAward
     */
    public function getAllGlobalAwards(Institution $institution)
    {
        return $this->doctrine->getRepository('InstitutionBundle:Institution')->getAllGlobalAwardsByInstitution($institution);
    }

    public function getAllInstitutionByParams($params)
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:Institution')->getAllInstitutionByParams($params);

        return $result;
    }

    /**
     * Note: Currently we have no way of flagging which user is the account owner.
     * We just rely on the behavior that the first user added to an institution,
     * which as a rule IS set to be the account owner, will be returned by doctrine
     * ArrayCollection->first(). (This observation has yet to be fully confirmed)
     *
     * @param mixed $institution Either institution id or an instance of Institution
     * @return InstitutionUser
     */
    public function getAccountOwner($institution)
    {
        if (is_numeric($institution)) {
            $institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find($institution);
        }

        if (!($institution instanceof Institution)) {
            throw new \Exception('Invalid institution');
        }

        //FIXME: getAccountData returns null
        return $this->institutionUserService->getAccountData($institution->getInstitutionUsers()->first());
        //return $this->institutionUserService->getAccountDataById($institution->getInstitutionUsers()->first()->getAccountId());
    }
    
    static public function institutionToArray(Institution $institution)
    {
        $data = array(
        	'id' => $institution->getId(),
            'name' => $institution->getName()
        );
        
        return $data;
        
    }
}