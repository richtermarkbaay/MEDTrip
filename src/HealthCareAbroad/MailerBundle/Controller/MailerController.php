<?php
namespace HealthCareAbroad\MailerBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MailerController extends Controller
{
    public function sendEmailAction(Request $request)
    {
        throw new NotFoundHttpException();

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('test@chromedia.com')
            ->setTo('harold.modesto@chromedia.com')
            ->setBody('You should see me from the profiler!')
        ;

        $this->get('mailer')->send($message);

        return new Response('Check the profiler!' , 200);
    }
}