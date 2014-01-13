<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use Doctrine\Common\Collections\ArrayCollection;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

class ContactDetailService
{
	protected $doctrine;
	
	/**
	 * @var ContactDetailService
	 */
	private static $instance = null;
	
	public function __construct()
	{
	    static::$instance = $this;
	}
	
	/**
	 * @var ContactDetailService
	 */
	private $contactDetailService;
	
	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function getContactDetailById($id)
	{
	    
	    $contactDetail = $this->doctrine->getRepository('HelperBundle:ContactDetail')->find($id);
	    
	    return $contactDetail;
	}
	
	public function save(ContactDetail $contactDetail)
	{
	    $em = $this->doctrine->getEntityManager();
	    $em->persist($contactDetail);
	    $em->flush();
	    
	    return $contactDetail;
	}
	/**
	 * Hackish way to use this service without injecting it on other services.
	 * 
	 * @return \HealthCareAbroad\HelperBundle\Services\ContactDetailService
	 */
	public static function getCurrentInstance()
	{
	    return self::$instance;
	}
	
	public function setContactDetailByContactArray($contactArray)
	{
        $arr = new ArrayCollection();
	    if($contactArray["phone_number"]) {
	        $contactDetail = new ContactDetail();
               $phoneContact = $contactArray["phone_number"];
               $contactDetail->setType(ContactDetail::TYPE_PHONE);
               $contactDetail = $this->setContactDetail($contactDetail, $phoneContact);
               $arr->add($contactDetail);
        }
        if($contactArray["mobile_number"]) {
            $contactDetail = new ContactDetail();
            $mobileContact = $contactArray["mobile_number"];
            $contactDetail->setType(ContactDetail::TYPE_MOBILE);
            $contactDetail = $this->setContactDetail($contactDetail, $phoneContact);
            $arr->add($contactDetail);
        }

        return $arr;
	}
	
	public function setContactDetail(ContactDetail $contactDetail, $contactsArray)
	{
	    $contactDetail->setType($contactsArray['type']);
	    $contactDetail->setCountryCode($contactsArray['country_code']);
	    $contactDetail->setNumber($contactsArray['number']);
	    $contactDetail->setAbbr($contactsArray['abbr']);

	    return $contactDetail;
	}
	
	/**
	 * Remove Contact Details
	 * Service is use for every submissions of data that has contact details object
	 *@author Chaztine Blance
	 */
    public function removeInvalidContactDetails($objectEntity)
    {
        foreach ($objectEntity->getContactDetails() as $contactDetail){
            if($contactDetail->getNumber() == NULL){
                $objectEntity->removeContactDetail($contactDetail);
            }
        }
        return $objectEntity;
    }
    
    /**
     * Check if user has a contact details (mobile or phone).
     * @param InstitutionUser $user
     * @return InstitutionUser
     * @author Chaztine Blance
     */
    
    public function initializeContactDetails($parentObject, $types, Country $country = null)
    {
        $types = array_flip($types);

        foreach ($parentObject->getContactDetails() as $contact){
            unset($types[$contact->getType()]);
        }

        foreach($types as $type => $dummy) {
            $number = new ContactDetail();
            $number->setType($type);
            if ($country){
                $number->setCountryCode($country->getCountryCode());
                $number->setCountry($country);
            }
            $parentObject->addContactDetail($number);
        }

        return $parentObject;
    }
    
    public function contactDetailToString($contactDetail)
    {
        $contactDetailInstance = null;
        if ($contactDetail instanceof ContactDetail){
            $contactDetailInstance = $contactDetail;
        }
        elseif (\is_array($contactDetail)) {
            // hydrated as array
            $contactDetailInstance = new ContactDetail();
            $contactDetailInstance->setCountryCode($contactDetail['countryCode']);
            $contactDetailInstance->setAreaCode($contactDetail['areaCode']);
            $contactDetailInstance->setNumber($contactDetail['number']);
            $contactDetailInstance->setType($contactDetail['type']);
        }
        
        
        return $contactDetailInstance 
            ? $contactDetailInstance->__toString()
            : null;
    }
    
    /**
     * Check if Contact Number type is phone and return string value.
     * @author Chaztine Blance 
     */
    public function getContactDetailsStringValue($objectEntity){
        
        $output = '';
        if($objectEntity){
            foreach ($objectEntity as $keys => $a){
                if($a->getType() == ContactDetailTypes::PHONE){
                    $output = $a->__toString();
                }
            }
        }
        
        return $output;
    }
}