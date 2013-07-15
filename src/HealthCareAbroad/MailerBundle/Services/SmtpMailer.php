<?php
namespace HealthCareAbroad\MailerBundle\Services;

/**
 * This class was created primarily for the following issue:
 *
 * FIXME: We directly create the mailer as a temporary workaround. Testing on
 * staging server doesn't seem to work when using multiple mailer services. In
 * particular the mailer service is getting an instance of Swift_MailTransport
 * instead of using Swift_SmtpTransport. The cause is possibly a simple
 * misconfiguration.
 *
 * Once we find a way to inject dependencies via configuration our concrete classes
 * can directly implement the MailerInterface instead.
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
abstract class SmtpMailer implements MailerInterface
{
    /**
     *
     * @param array $data
     * @return Ambigous <Swift_Mailer, Swift_Mailer>
     */
    protected function getMailer($data)
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
}