<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

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
    const NAME = 'institutionMedicalCenter';
    
    const OPTION_REMOVED_FIELDS = 'removed_fields';
    
    const OPTION_BUBBLE_ALL_ERRORS = 'bubble_all_errors';
    
    private $options = array();
    
    private static $fieldNames = array(
        'name',
        'description',
        'businessHours',
        'city',
        'zipCode',
        'state',
        'contactEmail',
        'contactNumber',
        'address',
        'timezone'
    );
    
    function __construct($institution)
    {
        $this->institution = $institution;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            self::OPTION_REMOVED_FIELDS => array(),
            self::OPTION_BUBBLE_ALL_ERRORS => false,
            'is_hidden' => true,
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $medicalCenter = $this->options['data'];
        
        if (!$medicalCenter instanceof InstitutionMedicalCenter) {
            throw new \Exception(__CLASS__.' expects data to be instance of InstitutionMedicalCenter');
        }
        
        $imcProperty = new InstitutionMedicalCenterProperty();
        $this->_add($builder, 'name','text', array('label' => 'Name'));
        $this->_add($builder, 'description', 'textarea', array('label' => 'Details','attr' => array('class' => 'tinymce')));
        //->add('ancilliaryServices','institution_property_type_custom_form',array('property_path' => false))
        $this->_add($builder, 'businessHours', 'hidden');
        
        if ($options['is_hidden']) {
            $this->_add($builder, 'city', 'city_list', array('disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getCity())));
            $this->_add($builder, 'zipCode', 'integer', array('disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getZipCode())));
            $this->_add($builder, 'state', 'text', array('label' => 'State or Province','disabled' => 'disabled', 'virtual' => true, 'attr' => array('value' => $this->institution->getState())));
            $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email', 'virtual' => true,'attr' => array('value' => $this->institution->getContactEmail())));
            $this->_add($builder, 'contactNumber', 'contact_number', array('label' => 'Institution Phone Number','virtual' => true,'attr' => array('value' => $this->institution)));
            $this->_add($builder, 'address', 'detailed_street_address', array('label' => 'Unit or Room #,  Building, Street Details', 'attr' => array('value' => $this->institution->getAddress1())));                
            $this->_add($builder, 'timeZone', 'text', array('label' => 'Timezone', 'virtual' => true, 'disabled' => 'disabled'));
        }
    }
    
    private function _isRemoved($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_REMOVED_FIELDS]);
    }
    
    private function _add(FormBuilderInterface $builder, $fieldName, $fieldType, array $options=array())
    {
        if (!$this->_isRemoved($fieldName)) {
            if ($this->options[self::OPTION_BUBBLE_ALL_ERRORS]) {
                $options['error_bubbling'] = true;
            }
            $builder->add($fieldName, $fieldType, $options);
        }
    } 

    public function getName()
    {
        return self::NAME;
    }
    
    static public function getFieldNames()
    {
        return static::$fieldNames;
    }
}