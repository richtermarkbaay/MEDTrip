<?php
namespace HealthCareAbroad\HelperBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\HelperBundle\Form\BreadcrumbFormType;

use HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree;

use DoctrineExtensions\NestedSet\Config;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilityController extends Controller
{
    public function manageBreadcrumbsAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $root = $service->getRepository()->find(1);
        
        $nodes = $service->getAllNodesOfTree($root);
        
        return $this->render('HelperBundle:Utility:index.html.twig', array('nodes' => $nodes));
    }
    
    public function changeParentBreadcrumbAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $wrappedNode = $service->getNode($request->get('id', 0));
        if (!$wrappedNode) {
            throw $this->createNotFoundException('Invalid breadcrumb');
        }
        
        if ($parentId = $request->get('parentId', 0)) {
            $parentNode = $service->getNode($parentId);
            if (!$parentNode) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
        }
        $node = $wrappedNode->getNode();
        
        $wrappedNode->moveAsFirstChildOf($parentNode);
        
        return $this->redirect($this->generateUrl('helper_utility_manageBreadcrumbs'));
    }
    
    public function addBreadcrumbAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        if ($parentId = $request->get('parentId', 0)) {
            $parentNode = $service->getNode($parentId);
            if (!$parentNode) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
        }
        
        $form = $this->createForm(new BreadcrumbFormType(), new BreadcrumbTree());
        
        return $this->render('HelperBundle:Utility:form.html.twig', array(
            'form' => $form->createView(),
            'parentId' => $parentId,
            'id' => 0,
            'parentPath' => $parentNode->getPath(' > ', true)
        ));
    }
    
    public function editBreadcrumbAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $wrappedNode = $service->getNode($request->get('id', 0));
        if (!$wrappedNode) {
            throw $this->createNotFoundException('Invalid breadcrumb');
        }
        $node = $wrappedNode->getNode();
        $form = $this->createForm(new BreadcrumbFormType(), $node);
    
        return $this->render('HelperBundle:Utility:form.html.twig', array(
            'form' => $form->createView(),
            'parentId' => 0,
            'id' => $node->getId(),
            'parentPath' => $wrappedNode->isRoot() ? '' : $wrappedNode->getParent()->getPath(' > ', true)
        ));
    }
    
    public function saveBreadcrumbAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return new Response("Method not allowed", 405);
        }
        
        $service = $this->get('services.breadcrumb_tree');
        if ($parentId = $request->get('parentId', 0)) {
            $parentNode = $service->getNode($parentId);
            if (!$parentNode) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
        }
        
        if ($id = $request->get('id', 0)) {
            $wrappedNode = $service->getNode($id);
            if (!$wrappedNode) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
            $form = $this->createForm(new BreadcrumbFormType(), $wrappedNode->getNode());
        }
        else {
            $form = $this->createForm(new BreadcrumbFormType(), new BreadcrumbTree());
        }
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            if ($id) {
                // we are in edit mode
                $node = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($node);
                $em->flush();
            }
            else {
                $node = $form->getData();
                $service->addChild($parentNode->getNode(), $node);
            }
            
            $request->getSession()->setFlash('success', 'Successfully saved breadcrumb '.$node->getLabel());
        }
        else {
            $request->getSession()->setFlash('error', 'Missing required fields in form');
        }
        
        return $this->redirect($this->generateUrl('helper_utility_manageBreadcrumbs'));
    }
}