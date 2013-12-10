<?php

namespace HealthCareAbroad\AdminBundle\Form;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCityListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Form\ListType\MedicalProviderGroupListType;

use HealthCareAbroad\MediaBundle\Form\InstitutionMediaFileType;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\HelperBundle\Form\FieldType\LocationFieldType;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionFormException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * Use this form when dealing with forms involving the Institution entity
 *
 * @author Allejo Chris G. Velarde
 */
class InstitutionProfileFormType extends AbstractType
{
    
    /**
     * @var the name of this form
     */
    const NAME = 'institution_profile_form';

    /**
     * @var unknown_type
     */
    const OPTION_HIDDEN_FIELDS = 'hidden_fields';

    const OPTION_REMOVED_FIELDS = 'removed_fields';

    const OPTION_BUBBLE_ALL_ERRORS = 'bubble_all_errors';

    private $options;

    private $institution;

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

    public function __construct(array $options = array())
    {
        $this->getDefaultOptions($options);
    }

    public function getName()
    {
        return self::NAME;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            self::OPTION_HIDDEN_FIELDS => array('type'),
            self::OPTION_REMOVED_FIELDS => array(),
            self::OPTION_BUBBLE_ALL_ERRORS => false,
            'validation_groups' => array('adminEditInstitutionProfile', 'Default')
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $this->institution = $builder->getData();
        $status = InstitutionStatus::getBitValueLabels();
        if (!$this->institution instanceof Institution ) {
            throw InstitutionFormException::nonInstitutionFormData(__CLASS__, $this->institution);
        }

        $this->_add($builder, 'name', 'text');
        $this->_add($builder,'status', 'choice', array('label' => 'Status', 'choices' => $status));
        $this->_add($builder, 'description', 'textarea', array('required' => false));
        $this->_add($builder, 'medicalProviderGroups', 'collection', array('type' => 'medicalProviderGroup_list', 'allow_add' => true, 'allow_delete' => true,'options'  => array( 'required' => false)));
        $this->_add($builder, 'country', FancyCountryFieldType::NAME, array('label' => 'Country', 'error_bubbling' => false));
        $this->_add($builder, 'city', GlobalCityListType::NAME, array('label' => 'City' , 'error_bubbling' => false));
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Zip / Postal Code'));
        $this->_add($builder, 'state', 'state_list', array('label' => 'State / Province' ));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email Address ', 'required' => false));
        $this->_add($builder, 'address1', 'detailed_street_address', array('label' => 'Hospital Address'));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'contactDetails', 'collection',array('type' => 'simple_contact_detail'));
        $this->_add($builder, 'websites', 'website_custom_field', array('label' => 'Website', 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field');
        $this->_add($builder, 'logo', new InstitutionMediaFileType($this->institution->getLogo()));
        $this->_add($builder, 'featuredMedia', new InstitutionMediaFileType($this->institution->getFeaturedMedia()));
        $this->_add($builder, 'coordinates', 'hidden');
        $this->_add($builder, 'type', 'choice', array ('label' => 'Institution Type' , 'empty_value' => false,'multiple' => false, 'choices' => InstitutionTypes::getFormChoices()));
    }

    private function _isHidden($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_HIDDEN_FIELDS]);
    }

    private function _isRemoved($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_REMOVED_FIELDS]);
    }

    private function _add(FormBuilderInterface $builder, $fieldName, $fieldType, array $options=array())
    {
        
        if ($this->_isRemoved($fieldName)) {

            // this field is flagged as removed, don't add this to builder
            return;
        }

        if ($this->_isHidden($fieldName)) {

            // check if this field is an object, default to get id as value
            if (\is_object($_currObject = $this->institution->{'get'.$fieldName}())) {

                $builder->add($fieldName, 'hidden', array('data' => $_currObject->getId()));
            }

        }
        else {
            if ($this->options[self::OPTION_BUBBLE_ALL_ERRORS]) {
                $options['error_bubbling'] = true;
            }

            $builder->add($fieldName, $fieldType, $options);
        }
    }

    /**
     * Helper function to get all possible fields of this form
     */
    static public function getFieldNames()
    {
        return static::$fieldNames;
    }
}