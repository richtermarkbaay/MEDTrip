<?php
namespace HealthCareAbroad\AdminBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;


use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\Factory\InstitutionMedicalCenterPropertyTypeFormFactory;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\InstitutionMedicalCenterPropertyValueCustomFieldType;

use Symfony\Component\Form\Util\PropertyPath;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
class InstitutionFormType extends AbstractType
{
    const NAME = 'institution';
    
    const OPTION_REMOVED_FIELDS = 'removed_fields';
    
    const OPTION_BUBBLE_ALL_ERRORS = 'bubble_all_errors';
    
    private $options = array();
    
    private static $fieldNames = array(
        'name',
        'description',
        'contactEmail',
        'contactNumber',
        'websites',
        'status'
    );
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            self::OPTION_REMOVED_FIELDS => array(),
            self::OPTION_BUBBLE_ALL_ERRORS => false,
            'is_hidden' => true,
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\Institution'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $institution = $this->options['data'];
        $status = InstitutionStatus::getBitValueLabels();
        if (!$institution instanceof Institution) {
            throw new \Exception(__CLASS__.' expects data to be instance of Institution');
        }
        
        $imcProperty = new InstitutionMedicalCenterProperty();
        $this->_add($builder, 'name','text', array('label' => 'Name'));
        $this->_add($builder, 'description', 'textarea', array('label' => 'Short description of the clinic', 'attr' => array('rows' => 5)));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email'));
        $this->_add($builder, 'contactNumber', 'contact_number', array('label' => 'Clinic Phone Number'));
        $this->_add($builder, 'websites', 'websites_custom_field');
        $this->_add($builder,'status', 'choice', array('label' => 'Status', 'choices' => $status));
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