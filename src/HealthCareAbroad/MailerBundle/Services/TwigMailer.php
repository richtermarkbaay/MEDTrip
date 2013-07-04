<?php
namespace HealthCareAbroad\MailerBundle\Services;

use Symfony\Bridge\Monolog\Logger;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class TwigMailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $twig;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, /*UrlGeneratorInterface $router,*/ \Twig_Environment $twig, array $parameters, Logger $logger)
    {
        $this->mailer = $mailer;
        //$this->router = $router;
        $this->twig = $twig;
        $this->parameters = $parameters;
        $this->logger = $logger;
    }

    /**
     * TODO: assess if passing in an instance of Swift_Message then filling in any
     * placeholders with the appropriate value is better. This way we don't have to
     * worry here if $context has the correct structure.
     *
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    public function sendMessage($context)
    {
        $context = $this->normalizeContext($context);
        $this->setupTransport($context);

        $template = $this->twig->loadTemplate($context['template']);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        //$this->logger->addInfo($htmlBody);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($context['to'])
            ->setFrom($context['user']);


        if (!empty($htmlBody)) {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $status = $this->mailer->send($message);

        $this->logger->addInfo('Mails sent: '.$status);
    }

    /**
     * Uses default values from config files if the key does not exist in $context
     *
     * @see \HealthCareAbroad\MailerBundle\Services\MailerInterface::normalizeContext()
     */
    public function normalizeContext($context)
    {
        if (!isset($context['templateConfig']) && !isset($context['template'])) {
            throw new \Exception('Please provide either a template configuration or directly reference a twig template.');
        }

        if (isset($context['user']) && isset($context['password'])) {
            // TODO: we have to modify mail transport to use this; gmail will
            // overwrite whatever we set the from field to with the email
            // address of gmail user account used
        }

        $defaultParameters = $this->parameters[$context['templateConfig']];

        foreach ($defaultParameters as $key => $value) {
            if (!isset($context[$key])) {
                $context[$key] = $value;
            }
        }

        return $context;
    }

    private function setupTransport($context)
    {
        $transport = $this->mailer->getTransport();
        $ext = $transport->getExtensionHandlers();
        $auth_handler = $ext[0];
        $auth_handler->setUserName($context['user']);
        $auth_handler->setPassword($context['password']);
    }

    // CONVENIENCE FUNCTIONS
    public function sendConfirmationEmailMessage(array $context = array())
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate('mailer_registration_confirm', array('token' => $user->getConfirmationToken()), true);
        $context = array(
                        'user' => $user,
                        'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    public function sendResettingEmailMessage(array $context = array())
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('mailer_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $context = array(
                        'user' => $user,
                        'confirmationUrl' => $url
        );
        $this->sendMessage($template, $context, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    // end CONVENIENCE FUNCTIONS
}