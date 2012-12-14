<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\Factory\InstitutionMedicalCenterPropertyTypeFormFactory;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\InstitutionMedicalCenterPropertyValueCustomFieldType;

use Symfony\Component\Form\Util\PropertyPath;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\InstitutionMedicalCenterPropertyCustomFormType;
class InstitutionMedicalCenterFormType extends AbstractType
{
    
    function __construct($institution)
    {
        $this->institution = $institution;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'is_hidden' => true,
                        'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $medicalCenter = $options['data'];
        
        $imcProperty = new InstitutionMedicalCenterProperty();
        $builder->add('name','text', array('label' => 'Name'))
        ->add('description', 'textarea', array('label' => 'Details','attr' => array('class' => 'tinymce')))
        //->add('ancilliaryServices','institution_property_type_custom_form',array('property_path' => false))
        ->add('businessHours', 'hidden');
        
        if ($options['is_hidden']) {
            $builder ->add('city', 'city_list', array('disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getCity())));
            $builder ->add('zipCode', 'integer', array('disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getZipCode())));
            $builder ->add('state', 'text', array('label' => 'State or Province','disabled' => 'disabled', 'virtual' => true, 'attr' => array('value' => $this->institution->getState())));
            $builder ->add('contactEmail', 'text', array('label' => 'Email', 'virtual' => true,'attr' => array('value' => $this->institution->getContactEmail())));
            $builder ->add('contactNumber', 'contact_number', array('label' => 'Institution Phone Number','virtual' => true,'attr' => array('value' => $this->institution)));
            $builder ->add('address', 'detailed_street_address', array('label' => 'Unit or Room #,  Building, Street Details', 'attr' => array('value' => $this->institution->getAddress1())));                
            $builder ->add('timeZone', 'text', array('label' => 'Timezone', 'virtual' => true, 'disabled' => 'disabled'));
        }
    }

    public function getName()
    {
        return 'institutionMedicalCenter';
    }
}