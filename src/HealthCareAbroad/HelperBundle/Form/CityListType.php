<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class CityListType extends AbstractType
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$result = $this->entityManager->getRepository('HelperBundle:City')->findByStatus(1);
    	$cities = array();
    	
    	foreach($result as $each) {
    		$cities[$each->getId()] = $each->getName();
    	}

        $resolver->setDefaults(array(
            'choices' => $cities
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'city_list';
    }
}