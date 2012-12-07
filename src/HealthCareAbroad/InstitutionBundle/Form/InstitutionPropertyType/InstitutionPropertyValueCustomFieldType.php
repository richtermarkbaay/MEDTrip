<?php
namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionPropertyValueCustomFieldType extends AbstractType
{
    protected $parent;
    
    protected $institutionProperty;
    
    public function __construct(InstitutionProperty $institutionProperty)
    {
        $this->parent = $institutionProperty->getInstitutionPropertyType()->getDataType()->getFormField();
        $this->institutionProperty = $institutionProperty;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->prependNormTransformer(new InstitutionPropertyValueTransformer());
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        
        if ('entity' == $this->parent) {
             
//             $data = $this->institutionProperty;
//             $defaultOptions['query_builder'] = function(EntityRepository $er) use ($data) {
//                 return $er->createQueryBuilder('ip')
//             };
            $resolver->setDefaults(array(
                'property' => 'name',
                'class' => $this->institutionProperty->getInstitutionPropertyType()->getDataClass()
            ));
        }
        
    }

    public function getName()
    {
        return 'institution_property_value_custom_field_type';
    }
    
    public function getParent()
    {
        return $this->parent;
    }
}