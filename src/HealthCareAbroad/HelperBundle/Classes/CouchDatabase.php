<?php

/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Classes;

use Guzzle\Http\Exception\ClientErrorResponseException;

use Guzzle\Service\Client;

class CouchDatabase {

    private $database;
    
    public function __construct($host, $port, $database) {
        $this->database = $database;
        $this->host = $host;
        $this->port = $port;
    }
    
    public function getBy($criteria = array())
    {
        foreach($criteria as $key => $value) {
            if(is_array($value) && isset($value['operator'])) {

                if(is_string($value['value'])) {
                    $value['value'] = "'" . $value['value'] . "'";
                }
                $conditions[] = "doc.$key " . $value['operator'] . ' ' . $value['value'];

            } else {
                $conditions[] = is_string($value['value']) ? "doc.$key == '$value'" : "doc.$key == $value";
            }
        }

        $conditions = implode(' && ', $conditions);
        $postData = array("map" => "function(doc){if($conditions) emit(doc.dateCreated, doc)}");
        $headers = array('Content-Type' => 'application/json');
        $response = $this->send('POST', '_temp_view?descending=true', $postData, $headers);

        return $response;
    }
    
    // GET an object
    public function get($id, $params = array('rev' => 0)) {
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

    // GET All object
    public function getAll($includeDocs = true) {
        $uri = '_all_docs';
        if($includeDocs) {
            $uri .= '?include_docs=true';
        }

        return $this->send('GET', $uri);
    }

    // GET an attachment
    public function getAttachment($id, $attachment) {
        return json_decode($this->send('GET', "$id/$attachment"));
    }

    // PUT an object
    public function put($id, $object) {
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
            $client = new Client($this->host. ":" . $this->port . "/". $this->database . "/");
            $response = $client->{strtolower($method)}($uri, $headers, json_encode($post_data))->send();

            //return $this->formatResponse($response);
            return $response->getBody()->__toString();

        } catch(ClientErrorResponseException $e) {
            return $e->getResponse()->getBody()->__toString();
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
?>