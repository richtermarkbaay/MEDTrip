<?php
namespace HealthCareAbroad\MailerBundle\Controller;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MailerController extends Controller
{
    public function sendEmailAction(Request $request)
    {
        throw new NotFoundHttpException();

//          $message = \Swift_Message::newInstance()
//              ->setSubject('Hello Email')
//              ->setFrom('test@chromedia.com')
//              ->setTo('harold.modesto@chromedia.com')
//              ->setBody('You should see me from the profiler!')
//          ;
        //$this->get('mailer')->send($message);

        $event = new GenericEvent(array('to' => 'haroldmodesto@gmail.com'));
        $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_TEST, $event);

        return new Response('Test' , 200);
    }


}