<?php
namespace HealthCareAbroad\MailerBundle\Services;

use Symfony\Bridge\Monolog\Logger;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class TwigMailer implements MailerInterface
{
    private $twig;
    private $logger;

    public function __construct(\Twig_Environment $twig, Logger $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function sendMessage($data)
    {
        $data = $this->normalizeData($data);

        $template = $this->twig->loadTemplate($data['template']);
        $subject = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($data['to'])
            ->setFrom($data['user']);

        if (isset($data['cc']) && !empty($data['cc'])) {
            $message->setCc($data['cc']);
        }

        if (isset($data['bcc']) && !empty($data['bcc'])) {
            $message->setBcc($data['bcc']);
        }

        if (empty($htmlBody)) {
            $message->setBody($textBody);
        } else {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        }

        $status = $this->getMailer($data)->send($message, $failures);

        $this->log('Mails sent: '.$status);
    }

    /**
     * We directly create the mailer as a temporary workaround. Testing on
     * staging server doesn't seem to work when using multiple mailer services.
     * In particular the mailer service is getting an instance of Swift_MailTransport
     * instead of using Swift_SmtpTransport. The cause is possibly a simple
     * misconfiguration.
     *
     * @param unknown $data
     * @return Ambigous <Swift_Mailer, Swift_Mailer>
     */
    private function getMailer($data)
    {
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl');
        $ext = $transport->getExtensionHandlers();
        $auth_handler = $ext[0];
        $auth_handler->setUserName($data['user']);
        $auth_handler->setPassword($data['password']);

        //For security:
        unset($data['password']);

        return \Swift_Mailer::newInstance($transport);
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

    private function log($message) {
        if (!is_null($this->logger)) {
            $this->logger->addInfo($message);
        }
    }
}