<?php 
namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
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
        return array(FormEvents::PRE_BIND => 'prebindSetData');
    }

    public function prebindSetData(DataEvent $event)
    {
    	$data = $event->getData();
        $form = $event->getForm();
        $countryId = !empty($data) && $data['country'] ? $data['country'] : 1;
        $form->add($this->factory->createNamed('city', new CityListType($countryId)));
    	
       
    }
}