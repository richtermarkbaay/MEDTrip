<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Query;

class InstitutionInquiryService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function save(InstitutionInquiry $institutionInquiry)
    {
        $em = $this->doctrine->getManager();
        $em->persist($institutionInquiry);
        $em->flush();
    }
    
    public function delete(InstitutionInquiry $institutionInquiry)
    {
        $em = $this->doctrine->getManager();
        $em->remove($institutionInquiry);
        $em->flush();
    }
    
    static public function toArray(InstitutionInquiry $institutionInquiry)
    {
        $data = array(
        	'id' => $institutionInquiry->getId(),
            'inquirerEmail' => $institutionInquiry->getInquirerEmail(),
            'inquirerName' => $institutionInquiry->getInquirerName(),
            'dateCreated' => $institutionInquiry->getDateCreated(),
            'country' => $institutionInquiry->getCountry(),
            'institution' => InstitutionService::institutionToArray($institutionInquiry->getInstitution()),
        );
        
        if ($imc=$institutionInquiry->getInstitutionMedicalCenter()) {
        	$data['institutionMedicalCenter'] = InstitutionMedicalCenterService::institutionMedicalCenterToArray($imc);
        }
        
        return $data;
    }

    public function getInquiriesByInstitution(Institution $institution)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a.id, a.inquirerName, a.inquirerEmail, a.message, a.dateCreated, a.status')
        ->from('InstitutionBundle:InstitutionInquiry', 'a')
        ->add('where', 'a.status != :status')
        ->andWhere('a.institution = :institution')
        ->setParameter('status', InstitutionInquiry::STATUS_DELETED)
        ->setParameter('institution', $institution)
        ->orderBy('a.dateCreated', 'DESC');

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
        ->setParameter('institution', $institution)
        ->orderBy('a.dateCreated', 'DESC');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}