<?php

namespace HealthCareAbroad\HelperBundle\Form\EventListener;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Symfony\Component\Form\Event\DataEvent;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactDetailDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => 'onPostBind');
    }
    
    public function onPostBind(DataEvent $event)
    {
        $contactDetail = $event->getData();
        $country = $contactDetail->getCountry();
        if ($country instanceof Country) {
            $code = (int)$country->getCountryCode();
            $contactDetail->setCountryCode($code);
        }
    }
}