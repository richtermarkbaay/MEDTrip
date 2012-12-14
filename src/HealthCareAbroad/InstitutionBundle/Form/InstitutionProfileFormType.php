<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\FieldType\LocationFieldType;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionFormException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionProfileFormType extends AbstractType
{
    const OPTION_HIDDEN_FIELDS = 'hidden_fields';
    
    private $options;
    
    public function getName()
    {
        return 'institution_profile_form';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            self::OPTION_HIDDEN_FIELDS => array(),
            'validation_groups' => array('editInstitutionInformation', 'Default')
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institution = $builder->getData();
        if (!$institution instanceof Institution ) {
            throw InstitutionFormException::nonInstitutionFormData(__CLASS__, $institution);
        }
        
        $cityId = 0;
        if ($city = $builder->getData()->getCity()) {
            $cityId = $city->getId();
        }
        
        $subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);
        
        $this->options = $options;
        $this->_add($builder, 'name', 'text');
        $this->_add($builder, 'description', 'textarea');
        $this->_add($builder, 'country', 'globalCountry_list', array('attr' => array('onchange'=>'Location.loadCities($(this), '. $cityId . ')')));
        $this->_add($builder, 'city', 'city_list');
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Zip Code'));
        $this->_add($builder, 'state', 'text', array('label' => 'State / Province'));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email'));
        $this->_add($builder, 'address1', 'detailed_street_address');
        $this->_add($builder, 'contactNumber', 'contact_number', array('label' => 'Institution Phone Number'));
        $this->_add($builder, 'websites', 'websites_custom_field');
        
    }
    
    private function _isHidden($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_HIDDEN_FIELDS]);
    }
    
    private function _add(FormBuilderInterface $builder, $fieldName, $fieldType, array $options=array())
    {
        if ($this->_isHidden($fieldName)) {
            $builder->add($fieldName, 'hidden');
        }
        else {
            $builder->add($fieldName, $fieldType, $options);
        }
    }
}