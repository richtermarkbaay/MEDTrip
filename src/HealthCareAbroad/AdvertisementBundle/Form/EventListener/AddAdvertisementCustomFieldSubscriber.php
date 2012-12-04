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

class AddAdvertisementCustomFieldSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $advertisement;
    private $em;

    public function __construct(FormFactoryInterface $factory, $advertisement, $em)
    {
        $this->em = $em;
        $this->factory = $factory;
        $this->advertisement = $advertisement;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData', FormEvents::POST_SET_DATA => 'postSetData', FormEvents::POST_BIND => 'postBind');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        if(!$this->em) {
            return;
        }
        
        $collectionProperties = array();
        $collectionPropertyValues = array();
        
        foreach($data as $i => $each) {
            $property = $each->getAdvertisementPropertyName();
        
            if($property->getDataType()->getFormField() == 'entity') {

                $newValue = $this->em->getRepository($property->getDataClass())->find($each->getValue());
                
                if(!isset($collectionPropertyValues[$property->getId()])) {
                    $collectionProperties[] = $each;
                    $collectionPropertyValues[$property->getId()] = new ArrayCollection();

                } else {
                    $data->removeElement($each);
                    unset($form[$i]);
                }

                $collectionPropertyValues[$property->getId()]->add($newValue);
            }
        }

        foreach($collectionProperties as $each) {
            $propertyId = $each->getAdvertisementPropertyName()->getId();
            $each->setValue($collectionPropertyValues[$propertyId]);
        }


    }

    public function postSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        
        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data) {
            return;
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

    public function postBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $transformData = array();

        foreach($data as $i => $each) {
            foreach($each->getValue() as $j => $property) {

                if(is_object($property)) {
                    if($j == 0) {
                        $event->getData()->get($i)->setValue($property->getId());
                    } else {
                        $newData = clone $event->getData()->get($i);
                        $newData->setValue($property->getId());
                        $event->getData()->add($newData);
                    }

                } else {
                    $event->getData()->get($i)->setValue($each->getValue()->getId());
                }
            }
        }
    }
}