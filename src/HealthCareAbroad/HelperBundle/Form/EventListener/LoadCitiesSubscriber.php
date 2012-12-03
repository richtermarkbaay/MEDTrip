<?php 
namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use HealthCareAbroad\HelperBundle\Services\LocationService;

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
        return array(FormEvents::PRE_BIND => 'prebind',
                        FormEvents::PRE_SET_DATA => 'preSetData');
    }
    
    public function preSetData(DataEvent $event)
    {
        
    }

    public function prebind(DataEvent $event)
    {
    	$data = $event->getData();
        $form = $event->getForm();
        
        $locationService = LocationService::getCurrentInstance();
        $countryId = !empty($data) && $data['country'] ? $data['country'] : 0;
        $cities = $locationService->getGlobalCitiesListByContry($countryId);
        $choices = array();
        foreach ($cities as $id => $value){
            $choices[$id] = $value['name'];
        }
        
        $form->add($this->factory->createNamed('city', 'city_list', null, compact('choices')));
    }
}