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
     * Transforms medicalCenters object to string medicalCenters separated by comma.
     *
     * @param ArrayCollection $centers
     * @return string
     */
    public function transform($centers)
    {
    	if(!count($centers)) {
    		return null;
    	}
    	
    	$centersName = array();
		foreach($centers as $center) {
			$centersName[] = $center->getName();
		}
		
		return implode(', ', $centersName);
    }

    /**
     * Transforms a string (centers) to an array object (centers).
     *
     * @param  string $stringCenters
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringCenters)
    {
    	$centerObjects = new ArrayCollection();

    	if($stringCenters == '')
    		return $centerObjects;

    	$centers = explode(',', $stringCenters);

    	foreach($centers as $centerName) {
    		$center = $this->em->getRepository('TreatmentBundle:Specialization')->findOneBy(array('name'=>trim($centerName)));
    		if($center) $centerObjects->add($center);
    	}

//         if (null === $issue) {
//             throw new TransformationFailedException(sprintf(
//                 'An issue with number "%s" does not exist!',
//                 $number
//             ));
//         }

		return $centerObjects;
    }
}