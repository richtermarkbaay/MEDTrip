<?php 
namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CampaignIntroController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('FrontendBundle:CampaignIntro:index.html.twig');
    }
}
?>