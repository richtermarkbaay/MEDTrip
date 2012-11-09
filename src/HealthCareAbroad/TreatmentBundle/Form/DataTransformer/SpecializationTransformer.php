<?php 

namespace HealthCareAbroad\TreatmentBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use Doctrine\ORM\EntityManager;

class SpecializationsTransformer implements DataTransformerInterface
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
     * Transforms specializations object to string specialization separated by comma.
     *
     * @param ArrayCollection $specializations
     * @return string
     */
    public function transform($specializations)
    {
    	if(!count($specializations)) {
    		return null;
    	}
    	
    	$specializationsName = array();
		foreach($specializations as $each) {
			$specializationsName[] = $each->getName();
		}
		
		return implode(', ', $specializationsName);
    }

    /**
     * Transforms a string (specializations) to an array object (specializations).
     *
     * @param  string $stringSpecializations
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringSpecializations)
    {
    	$specializationsObject = new ArrayCollection();

    	if($stringSpecializations == '')
    		return $specializationsObject;

    	$specializations = explode(',', $stringSpecializations);

    	foreach($specializations as $specializationName) {
    		$specialization = $this->em->getRepository('TreatmentBundle:Specialization')->findOneBy(array('name'=>trim($specializationName)));
    		if($specialization) $specializationsObject->add($specialization);
    	}

//         if (null === $issue) {
//             throw new TransformationFailedException(sprintf(
//                 'An issue with number "%s" does not exist!',
//                 $number
//             ));
//         }

		return $specializationsObject;
    }
}