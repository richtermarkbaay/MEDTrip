<?php 

namespace HealthCareAbroad\AdvertisementBundle\Form\EventListener;

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

    public function __construct(FormFactoryInterface $factory, $advertisement)
    {
        $this->factory = $factory;
        $this->advertisement = $advertisement;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_SET_DATA => 'postSetData', FormEvents::PRE_BIND => 'preBind', FormEvents::POST_BIND => 'postBind');
    }
    
    public function preBind(FormEvent $event)
    {
//         var_dump('preBind');
        
//         $data = $event->getData();
//         $form = $event->getForm();
        
//         var_dump($form->getData());
//         var_dump($this->advertisement);
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

//         // check if the product object is "new"
       if (!empty($data)) {

           $properties = $this->advertisement->getAdvertisementType()->getAdvertisementTypeConfigurations();
           $param = $this->advertisement->getInstitution(); // TODO - This param should be dynamic
           
           foreach($properties as $i => $each) {

               $config = json_decode($each->getPropertyConfig(), true);
               $type = $config['isClass'] ? new $config['type']($param) : $config['type'];

               foreach($form->all() as $i => $valueForm) {
//var_dump($valueForm->get('advertisementPropertyName')->getData()->getId());
                   if($each->getName() == $valueForm->get('advertisementPropertyName')->getData()->getName()) {

                       $fiedConfig = array_merge($config['config'], array('label' => $each->getLabel()));
                       
                       //var_dump($fiedConfig);
                       
                       $form->get($i)->add($this->factory->createNamed('value', $type, null, $fiedConfig));
                   }
               }
           }
           
           //exit;
       }
    }

    public function postBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $transformData = array();

//         var_dump('postBind');
//         var_dump($data);
//         var_dump($this->advertisement);
//         exit;
        
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