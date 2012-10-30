<?php 
/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class InstitutionAccountController extends Controller
{
	 public function accountAction(){
	 	
	 	return $this->render('InstitutionBundle:Institution:accountProfileForm.html.twig');
	 }
}