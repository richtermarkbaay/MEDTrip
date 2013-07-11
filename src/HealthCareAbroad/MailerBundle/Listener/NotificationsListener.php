<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class NotificationsListener
{
    protected $container;
    protected $templateConfigs;

    /**
     * Returns context dependent data needed by the email template
     *
     * @param Event $event
     * @return mixed $data
     */
    public abstract function getData(Event $event);

    /**
     * Returns the key used in the configuration for this email template
     *
     * @return string
    */
    public abstract function getTemplateConfig();

    public function __construct(ContainerInterface $container, array $templateConfigs)
    {
        $this->container = $container;
        $this->templateConfigs = $templateConfigs;
    }

    public function onSendNotification(Event $event)
    {
        try {
            $enabled = $this->container->getParameter('notifications.enabled');
        } catch (InvalidArgumentException $e) {
            return;
        }

        if (!$enabled) {
            return;
        }

        if (!$this->isEventProcessable($event)) {
            return;
        }

        $data = $this->mergeTemplateConfigData($this->getData($event));
        $data = $this->mergeTemplateSharedData($data);

        $this->container->get('services.mailer.notifications.twig')->sendMessage($data);
    }

    /**
     * Hook for custom or additional checks on whether to handle the event or not.
     *
     * @param Event $event
     * @return boolean
     */
    public function isEventProcessable(Event $event)
    {
        return true;
    }

    /**
     *
     * @param array $data
     * @throws \Exception
     * @return Ambigous <multitype:, unknown>
     */
    private function mergeTemplateConfigData(array $data)
    {
        if (!isset($this->templateConfigs[$this->getTemplateConfig()])) {
            throw new \Exception('Template configuration does not exist in the mailer.templates parameter. Check that getTemplateConfig() returns a valid value.');
        }

        //TODO: recursive merge
        foreach ($this->templateConfigs[$this->getTemplateConfig()] as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Shared data will be overridden by userland data.
     *
     * Data is hardcoded for now.
     *
     * TODO: recursive merge for more than two levels
     *
     * @param array $data
     */
    private function mergeTemplateSharedData(array $data)
    {
        //Hardcode for now; if not use router to generate the urls
        $sharedData = array(
            'email' => array(
                'support' => 'support@healthcareabroad.com'
            ),
            'url' => array(
                'support' => 'http://support.healthcareabroad.com',
                'login' => 'http://www.healthcareabroad.com/institution/login',
                'advertising_guide' => 'http://www.healthcareabroad.com/advertising-guide.html'
            )
        );

        // Support two levels for now
        foreach ($sharedData as $key => $value) {
            if (isset($data[$key]) && $data[$key]) {
                if (!is_array($value)) {
                    continue;
                }
                foreach ($value as $subKey => $subValue) {
                    if (isset($data[$key][$subKey]) && $data[$key][$subKey]) {
                        continue;
                    } else {
                        $data[$key][$subKey] = $subValue;
                    }
                }
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}