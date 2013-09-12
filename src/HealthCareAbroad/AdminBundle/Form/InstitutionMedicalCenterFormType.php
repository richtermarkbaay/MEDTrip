<?php
namespace HealthCareAbroad\AdminBundle\Form;

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
        if (!$medicalCenter->getId()) {
            $medicalCenter->setWebsites($this->institution->getWebsites());
            $medicalCenter->setSocialMediaSites($this->institution->getSocialMediaSites());
            $medicalCenter->setAddress($this->institution->getAddress1());
            $medicalCenter->setAddressHint($this->institution->getAddressHint());
            $medicalCenter->setContactEmail($this->institution->getContactEmail());
        }
        $imcProperty = new InstitutionMedicalCenterProperty();
        $this->_add($builder, 'name','text', array('label' => 'Name'));
        $this->_add($builder, 'description', 'textarea', array('label' => 'Short Description Of The Clinic', 'attr' => array('rows' => 4)));
        $this->_add($builder, 'businessHours', 'fancy_business_hours');
        $this->_add($builder, 'state', 'state_list', array('label' => 'State or Province','disabled' => 'disabled', 'virtual' => true));
        $this->_add($builder, 'contactEmail', 'email', array('label' => 'Email'));
        $this->_add($builder, 'contactDetails', 'collection', array('label' => 'Clinic Phone Number', 'type' => 'simple_contact_detail'));
        $this->_add($builder,'status', 'choice', array('label' => 'Status', 'choices' => $status));
        $this->_add($builder, 'websites', 'website_custom_field', array('label' => 'Clinic Website', 'required' => false));
        $this->_add($builder, 'socialMediaSites', 'social_media_sites_custom_field', array('attr' => array('value' => $this->institution->getSocialMediaSites())));
        $this->_add($builder, 'address', 'detailed_street_address', array('label' => 'Address', 'attr' => array('value' => $this->institution->getAddress1())));
        $this->_add($builder, 'addressHint', 'text', array('label' => 'Helpful hint for getting there?', 'required' => false));
        $this->_add($builder, 'timeZone', 'text', array('label' => 'Timezone', 'virtual' => true, 'disabled' => 'disabled'));

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