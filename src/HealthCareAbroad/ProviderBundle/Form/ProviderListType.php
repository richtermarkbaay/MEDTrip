<?php
namespace HealthCareAbroad\ProviderBundle\Form;

use HealthCareAbroad\ProviderBundle\Entity\Provider;
use HealthCareAbroad\ProviderBundle\Repository\ProviderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class ProviderListType extends AbstractType
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$result = $this->entityManager->getRepository('ProviderBundle:Provider')->findByStatus(1);
    	$providers = array();
    	
    	foreach($result as $each) {
    		$providers[$each->getId()] = $each->getName();
    	}
 
        $resolver->setDefaults(array(
            'choices' => $providers
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'provider_list';
    }
}