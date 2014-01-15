<?php
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionInquiryService
{
    protected $doctrine;
    
    function setDoctrine(Registry $r)
    {
        $this->doctrine = $r;
    }

    public function getInquiriesByInstitution(Institution $institution)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a.id, a.inquirerName, a.inquirerEmail, a.message, a.dateCreated, a.status')
        ->from('InstitutionBundle:InstitutionInquiry', 'a')
        ->add('where', 'a.status != :status')
        ->andWhere('a.institution = :institution')
        ->setParameter('status', InstitutionInquiry::STATUS_DELETED)
        ->setParameter('institution', $institution);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function updateInquiryStatus(InstitutionInquiry $inquiry, $status)
    {
        $inquiry->setStatus($status);
        $em = $this->doctrine->getEntityManager();
        $em->persist($inquiry);

        return $em->flush();
    }

    /**
     * 
     * @param array $inquiryIds
     * @param unknown_type $status
     */
    public function updateInquiriesStatus(array $inquiryIds, $status)
    {
        $inquiries = $this->doctrine->getRepository('InstitutionBundle:InstitutionInquiry')->findById($inquiryIds);
        $em = $this->doctrine->getEntityManager();

        foreach($inquiries as $inquiry) {
            $inquiry->setStatus($status);
            $em->persist($inquiry);
        }

        return $em->flush();
    }

    public function getInquiriesByInstitutionAndStatus(Institution $institution, $status)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a.id, a.inquirerName, a.inquirerEmail, a.message, a.dateCreated, a.status')
        ->from('InstitutionBundle:InstitutionInquiry', 'a')
        ->where('a.status = :status')
        ->andWhere('a.institution = :institution')
        ->setParameter('status', $status)
        ->setParameter('institution', $institution);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}