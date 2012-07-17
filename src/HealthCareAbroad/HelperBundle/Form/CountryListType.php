<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class CountryListType extends AbstractType
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$result = $this->entityManager->getRepository('HelperBundle:Country')->findByStatus(1);
    	$countries = array();
    	
    	foreach($result as $each) {
    		$countries[$each->getId()] = $each->getName();
    	}
 
        $resolver->setDefaults(array(
            'choices' => $countries
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'country_list';
    }
}