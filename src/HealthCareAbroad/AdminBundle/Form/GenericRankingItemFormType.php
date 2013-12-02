<?php
/**
 * Generic form type for updating ranking of an institution or an institution medical center
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenericRankingItemFormType extends AbstractType
{
    const NAME = 'rankingItem';
    
    public function getName()
    {
        return self::NAME;
    }
    
    public function buildForm(FormBuilderInterface$builder, array $options=array())
    {
        $builder->add('id', 'hidden')
        ->add('rankingPoints', 'hidden');    
    }    
}