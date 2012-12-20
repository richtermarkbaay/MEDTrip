<?php 

/**
 * Institution Specialization Transformer
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use Doctrine\ORM\EntityManager;

class SpecializationTransformer implements DataTransformerInterface
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
     * Transforms specializations object to string tags separated by comma.
     *
     * @param ArrayCollection $tags
     * @return string
     */
    public function transform($specializationGroupName)
    {
	  	if(!count($specializationGroupName)) {
    		return null;
    	}
    	$specializationGroupName = $specializationGroupName->getName();
    	
		return $specializationGroupName;
    }

    /**
     * Transforms a string (specialization) to an array object (specialization).
     *
     * @param  string $stringSpecializations
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringSpecializations)
    {
        
    	if($stringSpecializations == '')
    		return $specializationObjects;

		$specialization = $this->em->getRepository('TreatmentBundle:Specialization')->findOneBy(array('name'=>trim($stringSpecializations)));
	
		return $specialization;
    }
}