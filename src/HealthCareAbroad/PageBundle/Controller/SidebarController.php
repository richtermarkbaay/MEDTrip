<?php

namespace HealthCareAbroad\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SidebarController extends Controller
{
    public function popularAction()
    {
        return $this->render('PageBundle:Sidebar:popularPost.html.twig', array());
    }
    
    public function recentAction()
    {
    	return $this->render('PageBundle:Sidebar:recent.html.twig', array());
    }
    public function commentsAction()
    {
    	return $this->render('PageBundle:Sidebar:comments.html.twig', array());
    }
    public function tagsAction()
    {
    	return $this->render('PageBundle:Sidebar:tags.html.twig', array());
    }
}
