<?php 
namespace HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\HelperBundle\Entity\City;
use Doctrine\ORM\EntityManager;

class CityListTransformer implements DataTransformerInterface
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
	 * Transforms cityList object to string cities separated by comma.
	 *
	 * @param ArrayCollection $cities
	 * @return string
	 */
	public function transform($cities)
	{
		if(!count($cities)) {
			return null;
		}
		 
		$centersName = array();
		foreach($cities as $city) {
			$cityName[] = $city->getName();
		}
	
		return implode(', ', $cityName);
	}
	
	/**
	 * Transforms a string (cities) to an array object (cities).
	 *
	 * @param  string $stringCities
	 * @return ArrayCollection|null
	 */
	public function reverseTransform($stringCities)
	{
		$cityObjects = new ArrayCollection();
	
		if($stringCities == '')
			return $cityObjects;
	
		$centers = explode(',', $stringCities);
		foreach($cities as $cityName) {
			$city = $this->em->getRepository('HelperBundle:City')->findOneBy(array('name'=>trim($cityName)));
			if($city) $cityObjects->add($city);
		}
	
		//         if (null === $issue) {
		//             throw new TransformationFailedException(sprintf(
		//                 'An issue with number "%s" does not exist!',
		//                 $number
		//             ));
		//         }
	
		return $cityObjects;
	}
}

