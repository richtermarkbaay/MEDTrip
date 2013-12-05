<?php
namespace HealthCareAbroad\HelperBundle\Exception;

class LocationServiceException extends \Exception
{
    static public function failedApiRequest($requestUri, $responseBody)
    {
        throw new self(\sprintf('Failed request to API uri: %s with response: %s', $requestUri, $responseBody));
    }    
    
    static public function missingRequiredCountryDataKey($key)
    {
        throw new self(\sprintf('%s is required when hydrating Country', $key));
    }
    
    static public function missingRequiredStateDataKey($key)
    {
        throw new self(\sprintf('%s is required when hydrating State', $key));
    }
    
    static public function missingRequiredCityDataKey($key)
    {
        throw new self(\sprintf('%s is required when hydrating City', $key));
    }
}