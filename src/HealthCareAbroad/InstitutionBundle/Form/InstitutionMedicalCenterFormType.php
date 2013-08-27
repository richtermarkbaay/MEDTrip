<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

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
        'isAlwaysOpen',
        'businessHours',
        'city',
        'zipCode',
        'state',
        'contactEmail',
        'contactDetails',
        'address',
        'addressHint',
        'timezone',
        'websites',
        'socialMediaSites',
        'status',
        'services',
        'awards',
        'coordinates'
    );

    function __construct(Institution $institution = null)
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
        $status = InstitutionMedicalCenterStatus::getStatusList();
        if (!$medicalCenter instanceof InstitutionMedicalCenter) {
            throw new \Exception(__CLASS__.' expects data to be instance of InstitutionMedicalCenter');
        }
        $this->institution = $medicalCenter->getInstitution();

        $imcProperty = new InstitutionMedicalCenterProperty();
        $this->_add($builder, 'name','text', array('label' => 'Clinic Name'));
        $this->_add($builder, 'description', 'textarea', array('label' => 'Short description of the clinic', 'attr' => array('rows' => 4) , 'required' => false));
        $this->_add($builder, 'isAlwaysOpen', 'choice', array('choices' => array(1 => 'Open 24 hours a day 7 days a week', 0 => 'Specific Schedule'), 'expanded' => true));
        $this->_add($builder, 'businessHours', 'fancy_business_hours');

        $this->_add($builder, 'city', 'text', array('disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getCity())));
        $this->_add($builder, 'zipCode', 'text', array('label' => 'Zip / Postal Code ','disabled' => 'disabled', 'virtual' => true,'attr' => array('value' => $this->institution->getZipCode())));
        $this->_add($builder, 'state', 'text', array('label' => 'State or Province','disabled' => 'disabled', 'virtual' => true, 'attr' => array('value' => $this->institution->getState())));
        $this->_add($builder, 'country', 'text', array('label' => 'Country','disabled' => 'disabled', 'virtual' => true, 'attr' => array('value' => $this->institution->getCountry())));
        $this->_add($builder, 'contactEmail', 'text', array('label' => 'Email Address', 'required' => false));
        $this->_add($builder, 'contactDetails', 'collection', array('label' => 'Phone Number', 'type' => 'simple_contact_detail'));
        $this->_add($builder,'status', 'choice', array('label' => 'Status', 'choices' => $status));
        if (!$medicalCenter->getId()) {
            $medicalCenter->setWebsites($this->institution->getWebsites());
        }
        $this->_add($builder, 'websites', 'website_custom_field', array('label' => 'Website', 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field');
        $this->_add($builder, 'address', 'detailed_street_address', array('constraints' => array(new NotBlank(array('message' => 'Please provide a valid address.')))));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'timeZone', 'text', array('label' => 'Timezone', 'virtual' => true, 'disabled' => 'disabled'));
        $this->_add($builder, 'services', 'institutionServices_list', array('mapped' => false, 'centers' => true));
        $this->_add($builder, 'awards', 'institutionGlobalAwards_list', array('mapped' => false, 'centers' => true ));
            
        $this->_add($builder, 'coordinates', 'hidden', array('attr' => array('value' => ($medicalCenter->getCoordinates() ? $medicalCenter->getCoordinates() : $this->institution->getCoordinates()) )));

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