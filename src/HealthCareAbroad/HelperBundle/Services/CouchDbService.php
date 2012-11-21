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

    public function __construct($baseUrl, $kernelException) {
        $this->client = new Client($baseUrl);
        $this->kernelException = $kernelException;
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

    // Get _design/alerts/_view
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
    public function put($id, $object) 
    {
        return json_decode($this->send('PUT', "$id", $object), true);
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

    private function send($method, $uri, $post_data = null, $headers = array())
    {
        try {            
            $uri = $this->db . '/' . $uri;
            $response = $this->client->{strtolower($method)}($uri, $headers, json_encode($post_data))->send();

            return $response->getBody()->__toString();

        } catch(\Guzzle\Http\Exception\CurlException $e) {
            $this->kernelException->logException($e);
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
