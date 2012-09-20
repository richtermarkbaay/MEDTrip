<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class AdvertisementFormType extends AbstractType
{
    const OPTION_IS_NEW = 'isNew';
    
    const OPTION_FORCED_HIDDEN_FIELDS = 'forcedHiddenFields';
    
    const FIELD_ADVERTISEMENT_TYPE = 'advertisementType';
    
    const FIELD_INSTITUTION = 'institution';
    
    const FIELD_OBJECT = 'object';
    
    const FIELD_TITLE = 'title';
    
    const FIELD_DESCRIPTION = 'description';
    
    /**
     * @var FormBuilderInterface
     */
    private $builder;
    
    private $options;
    
    /**
     * @var Advertisement
     */
    private $advertisement;
    
    public function getName()
    {
        return 'advertisement';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(self::OPTION_IS_NEW => false, self::OPTION_FORCED_HIDDEN_FIELDS => array()));
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->builder = $builder;
        $this->options = $options;
        
        $isNew = isset($this->options[self::OPTION_IS_NEW]) && $this->options[self::OPTION_IS_NEW];
        $isNew ? $this->buildAddForm() : $this->buildEditForm();
    }
       
    private function buildAddForm()
    {
        if (!$this->isForcedHiddenField(self::FIELD_ADVERTISEMENT_TYPE)) {
            $this->builder->add(self::FIELD_ADVERTISEMENT_TYPE, 'choice', array('virtual' => true, 'choices' => AdvertisementTypes::getList()));
        }
        
        if (!$this->isForcedHiddenField(self::FIELD_INSTITUTION)) {
            $this->builder->add(self::FIELD_INSTITUTION, 'institution_list');
        }
        
        if (!$this->isForcedHiddenField(self::FIELD_OBJECT)) {
            $this->builder->add(self::FIELD_OBJECT, 'choice');
        }
        
        if (!$this->isForcedHiddenField(self::FIELD_TITLE)) {
            $this->builder->add(self::FIELD_TITLE, 'text');
        }
        
        if (!$this->isForcedHiddenField(self::FIELD_DESCRIPTION)) {
            $this->builder->add(self::FIELD_DESCRIPTION, 'textarea');
        }
        
        // render forced hidden fields
        foreach ($this->options[self::OPTION_FORCED_HIDDEN_FIELDS] as $field) {
            $this->builder->add($field, 'hidden');
        }
    }
   
    private function buildEditForm()
    {
        $this->advertisement = $this->options['data'];
        if (!$this->advertisement instanceof Advertisement) {
            throw new \Exception("Advertisement form requires HealthCareAbroad\AdvertisementBundle\Entity\Advertisement instance as data.");
        }
    }
    
    private function isForcedHiddenField($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_FORCED_HIDDEN_FIELDS]);
    }
}