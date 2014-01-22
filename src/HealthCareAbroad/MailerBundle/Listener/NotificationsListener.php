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
    protected $templateConfig;
    protected $exceptionLogger;
    private $debugMode;
    private $allowedRecipients;

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
    public abstract function getTemplateConfigName(Event $event = null);

    /**
     *
     * @param ContainerInterface $container
     * @param array $templateConfigs
     */
    public function __construct(ContainerInterface $container, array $templateConfigs)
    {
        $this->container = $container;
        $this->templateConfigs = $templateConfigs;
        $this->debugMode = $container->hasParameter('notifications.debug') ?
            $container->getParameter('notifications.debug') : false;
        $this->allowedRecipients = $container->hasParameter('notifications.allowed_recipients') ?
            $container->getParameter('notifications.allowed_recipients') : array();
    }

    public function setExceptionLogger(ExceptionLogger $logger)
    {
        $this->exceptionLogger = $logger;
    }

    /**
     * TODO: several of the checks here can be handled by compiler passes.
     *
     *
     * @param Event $event
     */
    public function onSendNotification(Event $event)
    {
        $templateConfigName = $this->getTemplateConfigName($event);

        if (isset($this->templateConfigs[$templateConfigName])) {
            $this->templateConfig = $this->templateConfigs[$templateConfigName];

            if (empty($this->templateConfig)) {
                throw new \Exception('Template configuration is required.');
            }
        } else {
            throw new \Exception('Template configuration does not exist in the mailer.templates parameter. Check that getTemplateConfig() returns a valid value.');
        }

        if (!$this->isEnabled($event)) {
            return;
        }

        if (!$this->isEventProcessable($event)) {
            return;
        }

        $data = $this->mergeTemplateConfigData($this->getData($event));
        $data = $this->mergeTemplateSharedData($data);

        if (!$this->hasAllowedRecipients($data)) {
            return;
        }

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
    private function mergeTemplateConfigData(array $data)
    {
        //TODO: recursive merge
        foreach ($this->templateConfig as $key => $value) {
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
                /*'support' => 'support@healthcareabroad.com'*/
                'support' => 'listings@healthcareabroad.com'
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

    private function isEnabled(Event $event)
    {
        try {
            //"global" parameter
            $enabled = $this->container->getParameter('notifications.enabled');
        } catch (InvalidArgumentException $e) {
            return false;
        }

        if ($enabled) {
            $enabled = isset($this->templateConfig['enabled']) && $this->templateConfig['enabled'];

            //Temporarily override specific notification setting when in debug mode.
            //The actual decision on whether to proceed or not will be made in
            //$this->hasAllowedRecipients
            if (!$enabled && $this->debugMode) {
                $enabled = true;
            }
        }

        return $enabled;
    }

    private function hasAllowedRecipients($data)
    {
        // Run this check again because we have temporarily ignored the
        // notifications enabled property in $this->isEnabled
        if (isset($this->templateConfig['enabled']) && $this->templateConfig['enabled']) {
            return true;
        }

        // if template is disabled check debug mode
        if (!$this->debugMode ) {
            return false;
        }

        // if in debug mode check if email is allowed
        if (!in_array(strtolower($data['to']), $this->allowedRecipients)) {
            return false;
        }

        // lastly remove cc if not in allowed list
        if (isset($data['cc']) && !in_array(strtolower($data['cc'], $this->allowedRecipients))) {
            unset($data['cc']);
        }

        return true;
    }
}