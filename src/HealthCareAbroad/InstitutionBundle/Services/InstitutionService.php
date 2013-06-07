<?php
/**
 * Service class for Institution
 * 
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use Symfony\Component\Validator\Constraints\DateTime;

use HealthCareAbroad\HelperBundle\Twig\TimeAgoExtension;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

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
    
    public function setTimeAgoExtension(\HealthCareAbroad\HelperBundle\Twig\TimeAgoTwigExtension $timeAgoExt)
    {
        $this->timeAgoExt = $timeAgoExt;
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

        static $isLoaded = false;

        if(!$isLoaded) {
            $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f, g, h, i, j')->from('InstitutionBundle:Institution', 'a')
            ->leftJoin('a.institutionMedicalCenters ', 'b', Join::WITH, 'b.status = :medicalCenterStatus')
            ->leftJoin('b.institutionSpecializations', 'c')
            ->leftJoin('c.specialization', 'd')
            ->leftJoin('c.treatments', 'e')
            ->leftJoin('a.country', 'f')
            ->leftJoin('a.city', 'g')
            ->leftJoin('a.logo', 'h')
            ->leftJoin('b.doctors', 'i')
            ->leftJoin('i.specializations', 'j')
            ->where('a.slug = :institutionSlug')
            ->andWhere('a.status = :status')
            ->setParameter('institutionSlug', $slug)
            ->setParameter('status', InstitutionStatus::getBitValueForApprovedStatus())
            ->setParameter('medicalCenterStatus', InstitutionMedicalCenterStatus::APPROVED);
            
            self::$institution = $qb->getQuery()->getOneOrNullResult();

            $isLoaded = true;
        }
        
        return self::$institution;
    }
    
    public function setInstitutionPropertyService(InstitutionPropertyService $v)
    {
        $this->institutionPropertyService = $v;
    }
    
    /**
     * Check if $institution is of type SINGLE_CENTER
     * 
     * @param Institution $institution
     * @return boolean
     */
    public function isSingleCenter(Institution $institution)
    {
        return InstitutionTypes::SINGLE_CENTER == $institution->getType();
    }
    
    /**
     * Check if $institution is of type MULTIPLE_CENTER
     * 
     * @param Institution $institution
     * @return boolean
     */
    public function isMultipleCenter(Institution $institution)
    {
        return InstitutionTypes::MULTIPLE_CENTER == $institution->getType();
    }
    
    /**
     * Get Institution Route Name
     *
     * @param Institution $institution
     * @return route name
     */
    public function getInstitutionRouteName(Institution $institution)
    {
        return $this->isMultipleCenter($institution)
            ? 'frontend_multiple_center_institution_profile'
            : 'frontend_single_center_institution_profile';
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
    
    
    /** TODO - Improved!
     * Get Filtered/Unique Institution Doctors
     * 
     * @author Adelbert Silla
     * @param Institution $institution
     * @return array Doctor
     */
    public function getAllDoctors(Institution $institution)
    {
        $institutionDoctors = array();
        foreach($institution->getInstitutionMedicalCenters() as $each) {
            $doctors = $each->getDoctors();

            foreach($doctors as $doctor) {
                if(!isset($institutionDoctors[$doctor->getId()])) {
                    $institutionDoctors[$doctor->getId()] = $doctor;                    
                }
            }
        }

        return $institutionDoctors;
    }
    
//     public function getBranches(Institution $institution)
//     {
//         $groupCriteria = array('medicalProviderGroups' => $this->institution->getMedicalProviderGroups()->first());
//         $institutionBranches = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->findBy($groupCriteria);
        
//         $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        
//         $qb->select('a')->from('InstitutionBundle:Institution')
//            ->leftJoin('a.medicalProviderGroups', 'b')
//            ->where($qb->expr()->in('a.id', $qb1->getDQL()));
//     }

    /**
     * @deprecated
     * @param Institution $institution
     * @param unknown_type $stepStatus
     */
    public function updateSignupStepStatus(Institution $institution, $stepStatus)
    {
//         if($stepStatus != $institution->getSignupStepStatus() && $institution->getSignupStepStatus() > 0) {
//             $institution->setSignupStepStatus($stepStatus);
//             $em = $this->doctrine->getEntityManager();

//             $em->persist($institution);
//             $em->flush($institution);
//         }
    }
    
    public function getListOfEmptyFieldsOnInstitution(InstitutionMedicalCenter $center)
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
        
        if($center->getDoctors()) {
            $emptyFields[] = 'doctors';
        }
        
        return $emptyFields;
    }
    
    public function getInstitutionInquiries(Institution $institution)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a')
        ->from('InstitutionBundle:InstitutionInquiry', 'a')
        ->add('where', 'a.status != :status')
        ->andWhere('a.institution = :institution')
        ->setParameter('status', InstitutionInquiry::STATUS_DELETED)
        ->setParameter('institution', $institution);
        
		return $qb->getQuery()
		->getResult();
    }
    
    public function getInstitutionInquiriesBySelectedTab(Institution $institution, $tab)
    {
        if($tab == "all") {
            $inquiries = $this->getInstitutionInquiries($institution);
        }
        elseif ($tab == "read") {
            $inquiries = $this->getInstitutionInquiriesByStatus($institution, InstitutionInquiry::STATUS_READ); 
        }
        else {
            $inquiries = $this->getInstitutionInquiriesByStatus($institution, InstitutionInquiry::STATUS_UNREAD); 
        }
        $inquiryArr = array();
        if(count($inquiries) != 0  ) {
            foreach ($inquiries as $each) {
                if($each->getStatus() == '1') {
                    $status = 'unread';
                }
                else {
                    $status = 'read';
                }
                $inquiryArr[] = array('sender' => $each->getInquirerEmail() ,
                                'id' => $each->getId(),
                                'message' => $each->getMessage(),
                                'status' => $status,         
                                'timeAgo' => $this->timeAgoExt->time_ago_in_words($each->getDateCreated()),
                                'viewPath' => $this->router->generate('institution_view_inquiry', array('id' => $each->getId())),
                                'removePath' => $this->router->generate('institution_delete_inquiry', array('id' => $each->getId())));
            
            }
        }
        
        return $inquiryArr;
    }
    
    public function setInstitutionInquiryStatus(InstitutionInquiry $inquiry, $status)
    {
        $inquiry->setStatus($status);
        $em = $this->doctrine->getEntityManager();
        $em->persist($inquiry);
        
        return $em->flush();
    }

    public function setInstitutionInquiryListStatus($inquiryList, $status)
    {
        $inquiryResultsList = $this->doctrine->getRepository('InstitutionBundle:InstitutionInquiry')->findById($inquiryList);
        $em = $this->doctrine->getEntityManager();
        
        foreach($inquiryResultsList as $inquiry) {
            $inquiry->setStatus($status);
            $em->persist($inquiry);
        }
        
        return $em->flush();
    }
    
    public function getInstitutionInquiriesByStatus(Institution $institution, $status)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionInquiry')->findBy(array('institution' => $institution, 'status' => $status));
    }
   
}