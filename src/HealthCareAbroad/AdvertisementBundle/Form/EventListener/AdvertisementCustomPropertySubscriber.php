<?php 

namespace HealthCareAbroad\AdvertisementBundle\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementCustomFormType;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class AdvertisementCustomPropertySubscriber implements EventSubscriberInterface
{
    private $factory;
    private $advertisement;

    public function __construct(FormFactoryInterface $factory, $advertisement)
    {
        $this->factory = $factory;
        $this->advertisement = $advertisement;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_SET_DATA => 'postSetData');
    }

    public function postSetData(FormEvent $event)
    {

        $data = $event->getData();
        $form = $event->getForm(); 
        
        if (null === $data) {
            return;
        }

        foreach($data as $i => $each) {
            $property = $each->getAdvertisementPropertyName();
            if($property->getDataType()->getColumnType() == 'collection' && is_string($each->getValue())) {
                unset($form[$i]);
            }
        }

        $properties = $this->advertisement->getAdvertisementType()->getAdvertisementTypeConfigurations();
        $param = $this->advertisement->getInstitution(); // TODO - This param should be dynamic

        foreach($properties as $i => $each) {

            $config = json_decode($each->getPropertyConfig(), true);
            $type = $config['isClass'] ? new $config['type']($param) : $config['type'];

            $fiedConfig = array_merge($config['config'], array('label' => $each->getLabel()));

            $form->get($i)->add($this->factory->createNamed('value', $type, null, $fiedConfig));
        }
    }
}