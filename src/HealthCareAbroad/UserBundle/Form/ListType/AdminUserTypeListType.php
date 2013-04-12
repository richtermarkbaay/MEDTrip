<?php
namespace HealthCareAbroad\UserBundle\Form\ListType;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminUserTypeListType extends AbstractType 
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'class' => 'HealthCareAbroad\UserBundle\Entity\AdminUserType',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('u') ->orderBy('u.name', 'ASC');
            }
            
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'userType_list';
    }
}