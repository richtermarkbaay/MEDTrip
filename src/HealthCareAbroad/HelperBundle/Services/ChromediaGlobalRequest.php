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
    
    private $default_uri = 'http://accounts.chromedia.com/app_dev.php';
    
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
    public function post($uri=null, $post_data=array(), $headers=array())
    {
        if ($uri === null) {
            $uri = $this->default_uri;
        }
        $response = $this->client->post($uri,$headers,$post_data)->send();
        
        return $response;
    }
}