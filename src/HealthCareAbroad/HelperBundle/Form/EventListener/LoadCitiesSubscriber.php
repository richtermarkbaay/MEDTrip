<?php 
namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCityListType;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;

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
                        FormEvents::POST_BIND => 'postBind');
    }
    
    public function preSetData(DataEvent $event)
    {
        
    }
    
    public function postBind(DataEvent $event)
    {
        $form = $event->getForm();
        $formData = $form->getData();
        $country = $formData->getCountry();
        $city = $formData->getCity();
        
        if ($city instanceof City && $country) {
            $city->setCountry($country);
        }
    }

    public function prebind(DataEvent $event)
    {
    	$data = $event->getData();
        $form = $event->getForm();
        
        $locationService = LocationService::getCurrentInstance();
        $countryId = !empty($data) && $data['country'] ? $data['country'] : 0;
        $choices = array(0 => null);
        if ($countryId) {
            $cities = $locationService->getGlobalCitiesListByContry($countryId);
            foreach ($cities['data'] as $id => $value){
                $choices[$id] = $value['name'];
            }    
        }

        $form->add($this->factory->createNamed('city', GlobalCityListType::NAME, null, compact('choices')));
    }
}