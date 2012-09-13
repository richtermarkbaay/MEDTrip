<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\HelperBundle\Entity\News',
			'query_builder' => function(EntityRepository $er){ return $er->getQueryBuilderForNews(); }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'news_list';
    }
}