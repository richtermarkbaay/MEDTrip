<?php
namespace HealthCareAbroad\MailerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailerControllerTest extends WebTestCase
{
//     public function testMailIsSentAndContentIsOk()
//     {
//         $client = static::createClient();

//         // Enable the profiler for the next request (it does nothing if the profiler is not available)
//         // Note: this is only available for symfony 2.2
//         // $client->enableProfiler();

//         // Check that the profiler is enabled
//         /*
//         if ($profile = $client->getProfile()) {
//             // check the number of requests
//             $this->assertLessThan(
//                             10,
//                             $profile->getCollector('db')->getQueryCount()
//             );

//             // check the time spent in the framework
//             $this->assertLessThan(
//                             500,
//                             $profile->getCollector('time')->getTotalTime()
//             );
//         }
//         */

//         $crawler = $client->request('POST', '/mail/test/send-email');


// /*
//         $mailCollector = $client->getProfile()->getCollector('swiftmailer');

//         // Check that an e-mail was sent
//         $this->assertEquals(1, $mailCollector->getMessageCount());

//         $collectedMessages = $mailCollector->getMessages();
//         $message = $collectedMessages[0];

//         // Asserting e-mail data
//         $this->assertInstanceOf('Swift_Message', $message);
//         $this->assertEquals('Hello Email', $message->getSubject());
//         $this->assertEquals('test@chromedia.com', key($message->getFrom()));
//         $this->assertEquals('harold.modesto@chromedia.com', key($message->getTo()));
//         $this->assertEquals(
//             'You should see me from the profiler!',
//             $message->getBody()
//         );
//     }*/
}