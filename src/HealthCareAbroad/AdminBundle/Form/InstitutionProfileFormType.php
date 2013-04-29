<?php

namespace HealthCareAbroad\AdminBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

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
        'country',
        'city',
        'zipCode',
        'state',
        'contactEmail',
        'address1',
        'addressHint',
        'contactNumber',
        'websites',
        'socialMediaSites',
        'services',
        'awards',
        'coordinates',
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
            self::OPTION_HIDDEN_FIELDS => array(),
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

        $cityId = 0;
        if ($city = $this->institution->getCity()) {
            $cityId = $city->getId();
        }
        // only add load cities subscriber if country is not hidden
        if (!$this->_isRemoved('country')) {
            $subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
            $builder->addEventSubscriber($subscriber);
        }

        $this->_add($builder, 'name', 'text', array('data' => ''));
        $this->_add($builder, 'description', 'textarea', array('required' => false));
        $this->_add($builder, 'country', 'globalCountry_list', array('attr' => array('onchange'=>'Location.loadCities($(this), '. $cityId . ')')));
        $this->_add($builder, 'city', 'city_list');
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Postal Code'));
        $this->_add($builder, 'state', 'text', array('label' => 'State / Province'));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Hospital Email Address '));
        $this->_add($builder, 'address1', 'detailed_street_address', array('label' => 'Hospital Address'));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'contactNumber', 'contact_number_with_flag', array('label' => 'Institution Phone Number', 'display_both' => false));
        $this->_add($builder, 'websites', 'text', array('label' => 'Hospital Website ' , 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field');
        $this->_add($builder, 'services', 'institutionServices_list', array('mapped' => false, 'centers' => false ));
        $this->_add($builder, 'awards', 'institutionGlobalAwards_list', array('mapped' => false, 'centers' => false ));
        $this->_add($builder, 'coordinates', 'hidden');
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