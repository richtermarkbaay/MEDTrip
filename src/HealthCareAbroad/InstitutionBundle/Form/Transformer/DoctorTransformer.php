<?php 
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\HelperBundle\Entity\Doctor;
use Doctrine\ORM\EntityManager;

class DoctorTransformer implements DataTransformerInterface
{
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @param EntityManager $om
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
     * Transforms tags object to string tags separated by comma.
     *
     * @param ArrayCollection $tags
     * @return string
     */
    public function transform($doctors)
    {
    	if(!count($doctors)) {
    		return null;
    	}
    	
    	$doctorName = array();
		foreach($doctors as $doctor) {
			$doctorName[] = $doctor->getName();
		}
		
		return implode(', ', $doctorName);
    }

    /**
     * Transforms a string (tags) to an array object (tag).
     *
     * @param  string $stringTags
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringDoctors)
    {
    	$doctorObjects = new ArrayCollection();

    	if($stringDoctors == '')
    		return $doctorObjects;

    	$doctors = explode(',', $stringDoctors);

    	foreach($doctors as $doctorName) {
    		$doctor = $this->em->getRepository('InstitutionBundle:Doctor')->findOneBy(array('name'=>trim($doctorName)));
    		if($doctor) $doctorObjects->add($doctor);
    	}

		return $doctorObjects;
    }
    
    
}

