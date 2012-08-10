<?php 
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

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
     * Transforms tags object to string tags separated by comma.
     *
     * @param ArrayCollection $tags
     * @return string
     */
    public function transform($cities)
    {
    	if(!count($cities)) {
    		return null;
    	}
    	
    	$cityName = array();
		foreach($cities as $city) {
			$cityName[] = $city->getName();
		}
		
		return implode(', ', $cityName);
    }

    /**
     * Transforms a string (tags) to an array object (tag).
     *
     * @param  string $stringTags
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringCities)
    {
    	$cityObjects = new ArrayCollection();

    	if($stringCities == '')
    		return $cityObjects;

    	$cities = explode(',', $stringCities);

    	foreach($cities as $cityName) {
    		$city = $this->em->getRepository('HelperBundle:City')->findOneBy(array('name'=>trim($cityName)));
    		if($city) $cityObjects->add($city);
    	}

		return $cityObjects;
    }
    
    
}

