<?php
/**
 * This is the base form for an advertisement. This form is only used for selecting AdvertisementType and institution. 
 * For more advertisement form details use type-specific form by using HealthCareAbroad\AdvertisementBundle\Services\AdvertisementFactory::createAdvertisementTypeSpecificForm 
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionTransformer;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var Advertisement
     */
    protected $advertisement;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
    }
    
    public function getName()
    {
        return 'advertisement';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            self::OPTION_IS_NEW => false, 
            self::OPTION_FORCED_HIDDEN_FIELDS => array()
        ));
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_ADVERTISEMENT_TYPE, 'choice', array('label' => 'Type', 'virtual' => true, 'choices' => AdvertisementTypes::getList()));
        $builder->add(self::FIELD_INSTITUTION, 'institution_list');
    }
    
    protected function buildCommon(FormBuilderInterface &$builder)
    {
        $builder->add('title', 'text', array('constraints' => array(new NotBlank())));
        $builder->add('description', 'textarea', array('constraints' => array(new NotBlank())));
        $builder->add($builder->create('institution', 'hidden')->prependNormTransformer(new InstitutionTransformer($this->container)));
    }
    
    protected function setFormData($options)
    {
        // data is passed so we require that it is an instance of Advertisement
        if (\array_key_exists('data', $options)) {
            if (!$options['data'] instanceof Advertisement) {
                $this->throwInvalidDataException();
            }
            $this->advertisement = $options['data']; 
        }
        else {
            $this->advertisement = null;
        }
    }
    
    protected function throwInvalidDataException()
    {
        throw new \Exception(__CLASS__." form requires HealthCareAbroad\AdvertisementBundle\Entity\Advertisement instance as data.");
    }
    
    private function isForcedHiddenField($fieldName)
    {
        return \in_array($fieldName, $this->options[self::OPTION_FORCED_HIDDEN_FIELDS]);
    }
    
    private function isAlwaysVirtualField($fieldName)
    {
        $virtualFields = array(self::FIELD_ADVERTISEMENT_TYPE);
        
        return \in_array($fieldName, $virtualFields);
    }
}