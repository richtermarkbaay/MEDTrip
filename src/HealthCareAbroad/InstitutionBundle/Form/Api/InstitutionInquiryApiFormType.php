<?php
namespace HealthCareAbroad\InstitutionBundle\Form\Api;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form type will be primarily be used by an API. This will only contain the core fields.
 * @author allejochrisvelarde
 *
 */
class InstitutionInquiryApiFormType extends AbstractType
{
    const NAME = 'institutionInquiry';
    public function getName()
    {
        return self::NAME;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: complete this
        $builder->add('inquirerEmail', 'text');
        $builder->add('inquirerName', 'text');
        $builder->add('status', 'hidden');
            
    }
}