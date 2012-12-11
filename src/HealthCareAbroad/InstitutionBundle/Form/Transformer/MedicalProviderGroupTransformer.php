<?php 
/**
 * Medical Provider Group Transformer
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;
use Doctrine\ORM\EntityManager;

class MedicalProviderGroupTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms languages object to string tags separated by comma.
     *
     * @param ArrayCollection $medicalProviderGroup
     * @return string
     */
    public function transform($medicalProviderGroups)
    {
    	if(!count($medicalProviderGroups)) {
    		return null;
    	}
    	$medicalProviderGroupName = $medicalProviderGroups->getName();
    	
		return $medicalProviderGroupName;
    }

    /**
     * Transforms a string (medicalProviderGroup) to an array object (medicalProviderGroup).
     *
     * @param  string $stringMedicalProviderGroups
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringMedicalProviderGroups)
    {
    	$medicalProviderGroupsObjects = new ArrayCollection();

    	if($stringMedicalProviderGroups == '')
    		return $medicalProviderGroupsObjects;

    		$medicalProviderGroup = $this->em->getRepository('InstitutionBundle:MedicalProviderGroup')->findOneBy(array('name'=>trim($stringMedicalProviderGroups)));
    		
    		// if medical Provider Exist return $medicalProviderGroupsObjects
    		if($medicalProviderGroup) { 
    		    
    		    $medicalProviderGroupsObjects->add($medicalProviderGroup);
    		}
    		// else create new medical provider and return $medicalProviderGroupsObjects
    		else{
    		    
    		    $medicalProviderGroup = new MedicalProviderGroup();
    		    $medicalProviderGroup->setName($stringMedicalProviderGroups);
    		    $medicalProviderGroup->setDescription('');
    		    $medicalProviderGroup->setStatus(MedicalProviderGroup::STATUS_ACTIVE);
    		    $this->em->persist($medicalProviderGroup);
    		    $this->em->flush();
    		    
    		    $medicalProviderGroupsObjects->add($medicalProviderGroup);
    		}
    		    		
		return $medicalProviderGroupsObjects;
    }
}