<?php 
/**
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form\EventListener;

use HealthCareAbroad\AdvertisementBundle\Form\HighlightFormType;
use HealthCareAbroad\MediaBundle\Entity\Media;

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
        return array(FormEvents::POST_SET_DATA => 'postSetData', FormEvents::PRE_BIND => 'preBind');
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
                $formField = $this->factory->createNamed('value', $type, null, $config['config']);

                $form->get($i)->add($formField);
            }
        }
    }
    
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $mediaClass = get_class(new Media());

        foreach($data as $each) {
            $newData[$each['advertisementPropertyName']] = $each;
        }

        foreach($form->getData() as $i => $each) {
            $property = $each->getAdvertisementPropertyName();
            if(isset($newData[$property->getId()])) {

                if($property->getName() == 'highlights') {
                    foreach($each->getValue() as $i => $highlight) {

                        if(!empty($highlight) && isset($newData[$property->getId()]['value']) && isset($newData[$property->getId()]['value'][$i]) && is_null($newData[$property->getId()]['value'][$i]['icon']) && is_object($highlight['icon']) && get_class($highlight['icon']) == $mediaClass) {
                            $newData[$property->getId()]['value'][$i]['icon'] = $highlight['icon'];
                        }
                    }
                }
                
                if($property->getName() == 'media_id' && isset($newData[$property->getId()]['value']) && is_null($newData[$property->getId()]['value']) && get_class($each->getValue()) == $mediaClass) {
                    //$newData[$property->getId()]['value'] = $each->getValue()->getId() ? $each->getValue() : '';
                    $newData[$property->getId()]['value'] = $each->getValue();
                }
            }
        }
        
        $event->setData(array_values($newData));
    }
}