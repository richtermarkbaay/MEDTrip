<?php
namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterPropertyValueCustomFieldType extends AbstractType
{
    protected $parent;
    
    protected $imcProperty;
    
    public function __construct(InstitutionMedicalCenterProperty $imcProperty)
    {
        $this->parent = $imcProperty->getInstitutionPropertyType()->getDataType()->getFormField();
        $this->imcProperty = $imcProperty;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->prependNormTransformer(new InstitutionMedicalCenterPropertyValueTransformer());
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
                'class' => $this->imcProperty->getInstitutionPropertyType()->getDataClass()
            ));
        }
        
    }

    public function getName()
    {
        return 'institution_medical_center_property_value_custom_field_type';
    }
    
    public function getParent()
    {
        return $this->parent;
    }
}