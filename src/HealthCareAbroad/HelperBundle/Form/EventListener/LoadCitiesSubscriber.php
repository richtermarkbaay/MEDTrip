<?php 
namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use HealthCareAbroad\HelperBundle\Form\CityListType;

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


        $countryId = !empty($data) && $data->getCountry() ? $data->getCountry()->getId() : 1;
		$form->add($this->factory->createNamed('city', new CityListType($countryId)));
    }
}