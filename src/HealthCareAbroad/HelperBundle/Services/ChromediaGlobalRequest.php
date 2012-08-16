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
    
    private $appId;
    
    private $appSecret;
    
    private $headers;
    
    public function __construct()
    {
        $this->client = new \Guzzle\Service\Client();
        $this->client->getEventDispatcher()->addListener('request.error', function(\Guzzle\Common\Event $event){
            
            $event->stopPropagation();
            
        });
    }
    
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }
    
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }
    
    /**
     * Send a GET request
     * 
     * @param string $uri
     * @return \Guzzle\Http\Message\Response
     */
    public function get($uri)
    {
        // add the authorization header
        $headers['Authorization'] = "Bearer {$this->_generateBearerToken()}";
        $headers['X-ApplicationId'] = $this->appId;
        $response = $this->client->get($uri,$headers)
            ->setAuth('developers.chromedia', 'cfe-developers', 'Bearer')
            ->send();
        
        return $response;
    }
    
    /**
     * Send a POST request
     * 
     * @param string $uri
     * @param array $post_data
     * @param array $headers
     * @return \Guzzle\Http\Message\Response
     */
    public function post($uri, $post_data=array(), $headers=array())
    {
        // add the authorization header
        $headers['Authorization'] = "Bearer {$this->_generateBearerToken()}";
        $headers['X-ApplicationId'] = $this->appId;
        $response = $this->client->post($uri,$headers,$post_data)
            ->setAuth('developers.chromedia', 'cfe-developers', 'Bearer')
            ->send();
        
        return $response;
    }
    
    private function _generateBearerToken()
    {
        return \ChromediaUtilities\Helpers\SecurityHelper::hash_sha256($this->appId.$this->appSecret);
    }
}