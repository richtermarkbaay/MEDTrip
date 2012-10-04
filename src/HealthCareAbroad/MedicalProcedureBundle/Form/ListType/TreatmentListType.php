<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form\ListType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TreatmentListType extends AbstractType 
{
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
        	'label' => 'Procedure Type',
			'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment',
// 			'query_builder' => function(EntityRepository $er) {
// 			    return $er->getQueryBuilderForGettingAvailableTreatments();
//             }
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'medicalproceduretype_list';
    }
}