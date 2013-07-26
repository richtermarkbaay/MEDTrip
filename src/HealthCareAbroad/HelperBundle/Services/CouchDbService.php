<?php

/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Services;

use Guzzle\Http\Exception\ClientErrorResponseException;

use Guzzle\Service\Client;

class CouchDbService {

    private $db;
    private $client;
    private $kernelException;
    private $isExistingBaseUrl;

    public function __construct($baseUrl, $kernelException = null) {
        $this->isExistingBaseUrl = $this->checkIfExistingBaseUrl($baseUrl);

        $this->client = new Client($baseUrl);
        //$this->kernelException = $kernelException;
    }

    // TODO - Temporary code for checking. Need Improvement
    function checkIfExistingBaseUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // set to HEAD request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't output the response
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_exec($ch);
        $result = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
        curl_close($ch);

        return $result;
    }

    public function setBaseUrl($baseUrl)
    {
        if($this->client)
            $this->client->setBaseUrl($baseUrl);
        else 
            $this->client = new Client($baseUrl);
    }

    public function setDatabase($dbName)
    {
        $this->db = $dbName;
    }

    public function getView($uri, $params = array()) {
        if(isset($params['keys'])) {
            $headers = array('Content-Type' => 'application/json');
            $result = $this->send('POST', $uri, $params, $headers);

        } else {
            $stringParams = '';
            foreach($params as $key => $val) {
                if(is_array($val)) {
                    $stringParams .= "&$key=" . json_encode($val);
                } else {
                    $stringParams .= '&' . (is_string($val) ? $key. '="' .$val .'"'  : "$key=$val");
                }
            }

            $result = $this->send('GET', "$uri?" . substr($stringParams, 1));
        }

        return $result;
    }

    // GET an object
    public function get($id, $params = array('rev' => 0)) 
    {
        if($params['rev'] == 0)
            unset($params['rev']);

        if (count($params)) {
            $strParams = http_build_query($params);            
            $response = $this->send('GET', "$id?$strParams");
        } else {
            $response = $this->send('GET', "$id");
        }

        return $response;
    }

    // GET an attachment
    public function getAttachment($id, $attachment) 
    {
        return json_decode($this->send('GET', "$id/$attachment"));
    }

    // PUT an object
    public function put($id = '', $object = null) 
    {
        return json_decode($this->send('PUT', $id, $object), true);
    }

    // DELETE an object
    public function delete($id, $rev) 
    {
        return json_decode($this->send('DELETE', "$id?rev=$rev"), true);
    }

    public function multipleUpdate($data = array())
    {
        $bulkData = array('docs' => $data);
        $headers = array('Content-Type' => 'application/json');
        return json_decode($this->send('POST', "_bulk_docs", $bulkData, $headers), true);
    }

    private function send($method, $uri = '', $post_data = null, $headers = array())
    {
        if($this->isExistingBaseUrl) {
            try {
                if($this->db)  {
                    $uri = $this->db . '/' . $uri;        
                }

                $response = $this->client->{strtolower($method)}($uri, $headers, json_encode($post_data))->send();
                
                return $response->getBody()->__toString();
    
             } catch(\Exception $e) {
                 //$this->kernelException->logException($e);
             }
        }
    }

    private function formatResponse($response)
    {
        $formattedResponse = array(
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $response->getBody()->__toString()
        );

        return $formattedResponse;
    }
}
