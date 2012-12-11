<?php 
/**
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvertisementPropertyValuesSubscriber implements EventSubscriberInterface
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

    /**
     * Transform advertisementPropertyValues form/field type based on advertisementPropertyName
     * Also removes duplicate advertisementPropertyName form/field
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm(); 

        if (null === $data) {
            return;
        }
        
        $param = $this->advertisement->getInstitution(); // TODO - This param should be dynamic
 
        foreach($data as $i => $each) {
            $property = $each->getAdvertisementPropertyName();

            if($property->getDataType()->getColumnType() == 'collection' && is_string($each->getValue())) {
                // Remove duplicate advertisementPropertyName form/field
                unset($form[$i]);
            } else {

                // Transform advertisementPropertyValues form/field type based on advertisementPropertyName
                $config = json_decode($property->getPropertyConfig(), true);
                $config['config']['label'] = $property->getLabel();
                $type = $config['isClass'] ? new $config['type']($param) : $config['type'];

                $form->get($i)->add($this->factory->createNamed('value', $type, null, $config['config']));
            }
        }
    }
}