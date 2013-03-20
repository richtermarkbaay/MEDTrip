<?php
/**
 * Functional test for FeedbackController
 */

namespace HealthCareAbroad\FrontendBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleWebTestCase;

class FeedbackControllerTest extends FrontendBundleWebTestCase
{
	public function testViewFeedBackodalAction()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/modal-feedback');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		
		//get the token
// 		$extract = $crawler->filter('input[name="feedbackMessage[_token]"]')->extract(array('value'));
// 		$csrf_token = $extract[0];
		
// 		$formValues =  array( 'feedbackMessage' => array(
// 		                'message' => 'test test',
// 		                'name' => 'test test',
// 		                'emailAddress' => 'test@yahoo.com',
// 		                '_token' => $csrf_token
// 		    ),
//                 array(),
//                 array('HTTP_X-Requested-With' => 'XMLHttpRequest')
// 		);
		
// 		$crawler = $client->request('POST', '/send-feedback', $formValues);
// 		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	
// 		$client = static::createClient();
// 		$crawler = $client->request('GET', '/send-feedback', $formValues);
// 		$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
		
// // 		// test for missing fields flow
// 		$client = static::createClient();
// 		$invalidFormValues = array(
// 				'feedbackMessage[message]' => '',
// 				'feedbackMessage[name]' => '',
// 				'feedbackMessage[emailAddress]' => 'test@yahoo.com',
//                 '_token' => $csrf_token
// 		);
// 		$crawler = $client->request('POST', '/send-feedback', $invalidFormValues);
// 		$this->assertTrue($crawler->filter('Failed')->count() == 0, 'Failed to update password ');
	}
	
	public function testSendFeedbackAction()
	{
		$client = static::createClient();
		
		//get the token
// 		$extract = $crawler->filter('input[name="feedbackMessage[_token]"]')->extract(array('value'));
// 		$csrf_token = $extract[0];

		$formValues =  array( 'feedbackMessage' => array(
						'captcha' => 'asdas',
						'country' => 1,
		                'message' => 'test test',
		                'name' => 'test test',
		                'emailAddress' => 'test@yahoo.com'
		    ),
                array(),
                array('HTTP_X-Requested-With' => 'XMLHttpRequest')
		);

		$crawler = $client->request('POST', '/send-feedback', $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		
	}
}

?>