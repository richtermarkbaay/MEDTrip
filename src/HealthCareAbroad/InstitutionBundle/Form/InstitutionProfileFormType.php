<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\MediaBundle\Form\InstitutionMediaFileType;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCityListType;
use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\InstitutionBundle\Exception\InstitutionFormException;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
        'services',
        'awards',
        'coordinates',
        'logo',
        'featuredMedia',
        'coordinates',
        'type'
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
            'validation_groups' => array('editInstitutionInformation', 'Default')
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $this->institution = $builder->getData();
        
        if (!$this->institution instanceof Institution ) {
            throw InstitutionFormException::nonInstitutionFormData(__CLASS__, $this->institution);
        }
        
        $this->_add($builder, 'name', 'text');
        $this->_add($builder, 'description', 'textarea', array('required' => false));
        $this->_add($builder, 'medicalProviderGroups', 'collection', array('type' => 'medicalProviderGroup_list', 'allow_add' => true, 'allow_delete' => true,'options'  => array( 'required' => false)));
        $this->_add($builder, 'country', FancyCountryFieldType::NAME, array('label' => 'Country', 'error_bubbling' => false, 'attr' => array('placeholder' => 'Type or select your Country')));
        $this->_add($builder, 'city', GlobalCityListType::NAME, array('attr' => array('placeholder' => 'Type or select your City'),'label' => 'City' , 'error_bubbling' => false));
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Zip / Postal Code'));
        $this->_add($builder, 'state', 'state_list', array('label' => 'State/Province','attr' => array('placeholder' => 'Type or select your State/Province')));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email Address ', 'required' => false));
        $this->_add($builder, 'address1', 'detailed_street_address', array('label' => 'Hospital Address'));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'contactDetails', 'collection',array('type' => 'simple_contact_detail'));
        $this->_add($builder, 'websites', 'website_custom_field', array('label' => 'Website', 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field');
        $this->_add($builder, 'services', 'institutionServices_list', array('mapped' => false, 'centers' => false));
        $this->_add($builder, 'awards', 'institutionGlobalAwards_list', array('mapped' => false, 'centers' => false));
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