<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Validator\Constraints\Blank;

use Doctrine\ORM\EntityManager;
use HealthCareAbroad\ListingBundle\Entity\ListingLocation;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use HealthCareAbroad\ProviderBundle\Form\ProviderListType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ListingType extends AbstractType
{
	private $em;
	
	function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$isProvider = false;
		if(!$isProvider) {
			//$listing = $this->get("services.provider")->getActiveProvidersList();
			//$this->em->getRepository('ProviderBundle:Provider')->getCityListByCountryId(1);
			//$transformer = new ProviderTransformer($this->em);
			//$builder->add('provider', 'choice', array('choices'=>array('1'=>'sdfsdf',2=>'sdfsdfsdf')))->addViewTransformer($transformer);
			$builder->add('provider', 'provider_list');
			//$builder->add('provider', 'provider_selector');
			
			$factory = $builder->getFormFactory();
			
			$builder->addEventListener(FormEvents::PRE_SET_DATA, function($event) use ($factory) {
				$form = $event->getForm();
				$room = $event->getData();
				if ($room) {
					$form->remove('provider');
					$form->add($factory->createNamed('provider', 'choice', null, array('choices' => array('1'=>'sdfsdf',2=>'sdfsdfsdf')
					)));
				}
			});
		}
		
		$builder->add('title');
		$builder->add('description');
		$builder->add('logo', 'file', array('required'=>false, 'constraints'=>array(new Blank())));

		$procedures = array('Hearth Churva','Bisan ano');
		$builder->add('procedure', 'choice', array('choices' => $procedures));
		$builder->add('location', new LocationType(),array('property_path'=>false));
// 		$countries = $this->em->getRepository('HelperBundle:Country')->getCountryList();
// 		$builder->add('country', 'choice', array('choices' => $countries, 'property_path' => false));
		
// 		$cities = $this->em->getRepository('HelperBundle:City')->getCityListByCountryId(1);
// 		$builder->add('city', 'choice', array('choices' => $cities, 'property_path' => false));
		
// 		$builder->add('address', 'textarea', array('property_path' => false));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'HealthCareAbroad\ListingBundle\Entity\Listing',
	    ));
	}

	
	
	public function getName()
	{
		return 'listing';
	}
}
