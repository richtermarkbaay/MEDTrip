<?php
/**
 * Service class for requesting global API
 * 
 * @author Allejo Chris G. Velarde
 * 
 */

namespace HealthCareAbroad\HelperBundle\Services;

class ChromediaGlobalRequest
{
    /**
     * 
     * @var \Guzzle\Service\Client
     */
    private $client;
    
    public function __construct()
    {
        $this->client = new \Guzzle\Service\Client();
        $this->client->getEventDispatcher()->addListener('request.error', function(\Guzzle\Common\Event $event){
            
            $event->stopPropagation();
            
        });
    }
    
    /**
     * 
     * @param string $uri
     * @param array $post_data
     * @param array $headers
     * @return \Guzzle\Http\Message\Response
     */
    public function post($uri, $post_data=array(), $headers=array())
    {
        $response = $this->client->post($uri,$headers,$post_data)->send();
        
        return $response;
    }
}