<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
	
class CityController  extends Controller
{
	public function loadCitiesAction($countryId)
	{
		$data = $this->get('services.location')->getListActiveCitiesByCountryId($countryId);
	
		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
	
}
?>