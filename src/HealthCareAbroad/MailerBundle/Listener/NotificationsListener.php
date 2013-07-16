<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\LogBundle\Services\ExceptionLogger;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class NotificationsListener
{
    protected $container;
    protected $templateConfigs;
    protected $exceptionLogger;

    /**
     * Returns context dependent data needed by the email template
     *
     * @param Event $event
     * @return mixed $data
     */
    public abstract function getData(Event $event);

    /**
     * Returns the key used in the configuration for this email template.
     * Returned value can vary depending on the event type.
     *
     * @param Event $event
     * @return string
    */
    public abstract function getTemplateConfig(Event $event = null);

    /**
     *
     * @param ContainerInterface $container
     * @param array $templateConfigs
     */
    public function __construct(ContainerInterface $container, array $templateConfigs)
    {
        $this->container = $container;
        $this->templateConfigs = $templateConfigs;
    }

    public function setExceptionLogger(ExceptionLogger $logger)
    {
        $this->exceptionLogger = $logger;
    }

    /**
     * If
     *
     *
     * @param Event $event
     */
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

        $data = $this->mergeTemplateConfigData($this->getData($event), $this->getTemplateConfig($event));
        $data = $this->mergeTemplateSharedData($data);

        try {
            $this->container->get('services.mailer.notifications.twig')->sendMessage($data);
        } catch (\Exception $e) {
            if ($this->propagateExceptions($event)) {
                throw $e;
            }

            //We are bypassing the system's internal exceptions handler so
            //we have to log the exception ourselves.
            //TODO: right now we are ignoring the exceptions; find a way to
            //inform client of the notifications error
            $this->exceptionLogger->logException($e);
        }
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
     * Whether or not exceptions should be propagated up the call stack.
     *
     * @param Event $event
     * @return boolean
     */
    public function propagateExceptions(Event $event)
    {
        return true;
    }

    /**
     *
     * @param array $data
     * @param string $templateConfig
     * @throws \Exception
     * @return Ambigous <multitype:, unknown>
     */
    private function mergeTemplateConfigData(array $data, $templateConfig)
    {
        if (!$templateConfig) {
            throw new \Exception('Template configuration is required.');
        }

        if (!isset($this->templateConfigs[$templateConfig])) {
            throw new \Exception('Template configuration does not exist in the mailer.templates parameter. Check that getTemplateConfig() returns a valid value.');
        }

        //TODO: recursive merge
        foreach ($this->templateConfigs[$templateConfig] as $key => $value) {
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