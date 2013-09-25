<?php
namespace HealthCareAbroad\MailerBundle\Services;

use Symfony\Bridge\Monolog\Logger;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class TwigMailer extends SmtpMailer
{
    private $twig;
    private $logger;
    private $debugMode;
    private $debugEmails;

    public function __construct(\Twig_Environment $twig, Logger $logger = null, $debugMode = false, $debugEmails = array())
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->debugMode = $debugMode;
        $this->debugEmails = $debugEmails;
    }

    public function sendMessage($data)
    {
        $data = $this->normalizeData($data);

        if ($this->debugMode) {
            //$debugEmails = array('hazel.caballero@pinoyoutsource.com', 'haroldmodesto@gmail.com', 'harold.modesto@chromedia.com');
            if (!in_array(strtolower($data['to']), $this->debugEmails)) {
                return;
            }
            if (isset($data['cc']) && !in_array(strtolower($data['cc'], $this->debugEmails))) {
                unset($data['cc']);
            }
        }

        $template = $this->twig->loadTemplate($data['template']);
        $subject = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($data['to'])
            ->setFrom($data['user']);

        if (isset($data['cc']) && $data['cc']) {
            $message->setCc($data['cc']);
        }

        if (isset($data['bcc']) && $data['bcc']) {
            $message->setBcc($data['bcc']);
        }

        if (empty($htmlBody)) {
            $message->setBody($textBody);
        } else {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        }

        try {
            $status = $this->getMailer($this->getMailAccountCredentials($data))->send($message, $failures);
        } catch (\Exception $e) {
            $this->log('Notification error.');
            throw $e;
        }
    }

    /**
     * Process data and modify it if necessary (e.g. sanity checks).
     *
     * @param array $data
     * @throws \Exception
     * @return mixed $data
     */
    private function normalizeData($data)
    {
        if (!isset($data['to']) || !$data['to']) {
            throw new \Exception('Mail recipient is required.');
        }

        if (!isset($data['user']) || !$data['user']) {
            throw new \Exception('Mail sender is required.');
        }

        if (!isset($data['password']) || !$data['password']) {
            throw new \Exception('Mail account credentials are required.');
        }

        return $data;
    }

    /**
     * For debugging
     *
     * @param string $message
     */
    private function log($message)
    {
        if (!is_null($this->logger)) {
            $this->logger->addInfo('>>>MailerBundle: ' . $message);
        }
    }

    private function getMailAccountCredentials($data)
    {
        $creds['password'] = $data['password'];
        $creds['user'] = is_array($data['user']) ? key($data['user']) : $data['user'];

        return $creds;
    }
}