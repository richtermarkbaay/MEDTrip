<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SidebarController extends Controller
{
    public function popularAction()
    {
        return $this->render('FrontendBundle:Sidebar:popularPost.html.twig', array());
    }
    
    public function recentAction()
    {
    	return $this->render('FrontendBundle:Sidebar:recent.html.twig', array());
    }
    public function commentsAction()
    {
    	return $this->render('FrontendBundle:Sidebar:comments.html.twig', array());
    }
    public function tagsAction()
    {
    	return $this->render('FrontendBundle:Sidebar:tags.html.twig', array());
    }
}
