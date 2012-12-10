<?php
/**
 * Form used for Helper Text
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\RouteType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\HelperText;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HelperTextFormType extends AbstractType
{
    // How does it work?
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'data_class' => 'HealthCareAbroad\HelperBundle\Entity\HelperText',
        ));
    }
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	       $builder->add('route', 'text', array('constraints' => array(new NotBlank())));
		   $builder->add('details', 'textarea');
	}

	public function getName()
	{
		return 'helper_text';
	}
}
