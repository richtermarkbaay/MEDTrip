<?php
namespace HealthCareAbroad\InstitutionBundle\Services;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use Doctrine\Bundle\DoctrineBundle\Registry;
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
        return array(
        	'id' => $institutionInquiry->getId(),
            'inquirerEmail' => $institutionInquiry->getInquirerEmail(),
            'inquirerName' => $institutionInquiry->getInquirerName(),
            'dateCreated' => $institutionInquiry->getDateCreated(),
            'country' => $institutionInquiry->getCountry()
        );
    }
}