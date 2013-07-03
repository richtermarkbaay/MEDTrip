<?php
namespace HealthCareAbroad\MailerBundle\Services;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class TwigMailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $twig;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->parameters = $parameters;
    }

    /**
     * TODO: assess if passing in an instance of Swift_Message then filling in any
     * placeholder with the appropriate value is better. This way we don't have to
     * worry if $context has the correct structure.
     *
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    public function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->normalizeContext($context);

        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }

    /**
     * Uses default values from config files if the key does not exist in $context
     *
     * @see \HealthCareAbroad\MailerBundle\Services\MailerInterface::normalizeContext()
     */
    public function normalizeContext(array $context = array())
    {
        if (!isset($context['notificationType'])) {
            throw new \Exception('Key "notificationType" is required');
        }

        $defaultParameters = $this->parameters[$context['notificationType']];

        foreach ($defaultParameters as $key => $value) {
            if (!isset($context[$key])) {
                $context['$key'] = $value;
            }
        }

        return $context;
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