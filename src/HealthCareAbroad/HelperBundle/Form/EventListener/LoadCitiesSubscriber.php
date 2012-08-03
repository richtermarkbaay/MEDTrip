<?php 
namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use Doctrine\Tests\DBAL\Types\VarDateTimeTest;

use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class LoadCitiesSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();	
		if($data) 
        {
        	var_dump($form);
        	var_dump($data);exit;
        
        }

        $countryId = !empty($data) && $data->getCountry() ? $data->getCountryId() : 1;
		$form->add($this->factory->createNamed('city', new CityListType($countryId)));
    }
}