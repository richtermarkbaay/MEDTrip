<?php
/**
 * NOTE: Currently being used to update institutionStatus only!
 */
namespace HealthCareAbroad\AdminBundle\Form;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCityListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\MediaBundle\Form\InstitutionMediaFileType;

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
        'medicalProviderGroups',
        'country',
        'city',
        'zipCode',
        'state',
        'contactEmail',
        'address1',
        'addressHint',
        'contactDetails',
        'websites',
        'socialMediaSites',
        'coordinates',
        'logo',
        'featuredMedia',
        'coordinates',
        'type',
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
        $this->_add($builder, 'name', 'text');
        $this->_add($builder,'status', 'choice', array('label' => 'Status', 'choices' => $status));
        $this->_add($builder, 'description', 'textarea', array('required' => false));
        $this->_add($builder, 'medicalProviderGroups', 'collection', array('type' => 'medicalProviderGroup_list', 'allow_add' => true, 'allow_delete' => true,'options'  => array( 'required' => false)));
        $this->_add($builder, 'country', FancyCountryFieldType::NAME, array('label' => 'Country', 'error_bubbling' => false));
        $this->_add($builder, 'city', GlobalCityListType::NAME, array('label' => 'City' , 'error_bubbling' => false));
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Zip / Postal Code'));
        $this->_add($builder, 'state', 'state_list', array( 'error_bubbling' => false ,'label' => 'State / Province' ));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email Address ', 'required' => false));
        $this->_add($builder, 'address1', 'detailed_street_address', array('label' => 'Hospital Address'));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'contactDetails', 'collection',array('type' => 'simple_contact_detail'));
        $this->_add($builder, 'websites', 'website_custom_field', array('label' => 'Website', 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field');
        $this->_add($builder, 'logo', new InstitutionMediaFileType($institution->getLogo()));
        $this->_add($builder, 'featuredMedia', new InstitutionMediaFileType($institution->getFeaturedMedia()));
        $this->_add($builder, 'coordinates', 'hidden');
        $this->_add($builder, 'type', 'choice', array ('label' => 'Institution Type' , 'empty_value' => false,'multiple' => false, 'choices' => InstitutionTypes::getFormChoices()));
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