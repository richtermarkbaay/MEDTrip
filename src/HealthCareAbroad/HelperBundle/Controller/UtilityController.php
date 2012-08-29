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
    public function manageInstitutionBreadcrumbsAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $root = $service->getRepository()->find(2);
        $nodes = $service->getAllNodesOfTree($root);
        $request->getSession()->set('referer', $this->generateUrl($request->get('_route')));
        
        return $this->render('HelperBundle:Utility:index.html.twig', array(
            'breadcrumbService' => $service,
            'nodes' => $nodes,
            'context' => 'institution'
        ));
    }
    
    public function manageAdminBreadcrumbsAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $root = $service->getRepository()->find(1);        
        $nodes = $service->getAllNodesOfTree($root);
        $request->getSession()->set('referer', $this->generateUrl($request->get('_route')));
        
        return $this->render('HelperBundle:Utility:index.html.twig', array(
            'breadcrumbService' => $service,
            'nodes' => $nodes,
            'context' => 'admin'
        ));
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
            'parentNode' => $parentNode,
            'id' => 0,
            'parentPath' => $service->getPathOfNode($parentNode, false)
        ));
    }
    
    public function editBreadcrumbAction(Request $request)
    {
        $service = $this->get('services.breadcrumb_tree');
        $node = $service->getNode($request->get('id', 0));
        if (!$node) {
            throw $this->createNotFoundException('Invalid breadcrumb');
        }
        $form = $this->createForm(new BreadcrumbFormType(), $node);
        $parentNode = $node->getParent();
    
        return $this->render('HelperBundle:Utility:form.html.twig', array(
            'form' => $form->createView(),
            'parentNode' => $parentNode,
            'id' => $node->getId(),
            'parentPath' => $service->getPathOfNode($parentNode, false)
        ));
    }
    
    public function saveBreadcrumbAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return new Response("Method not allowed", 405);
        }
        
        $service = $this->get('services.breadcrumb_tree');
        $parentNode = null;
        if ($parentId = $request->get('parentId', 0)) {
            $parentNode = $service->getNode($parentId);
            if (!$parentNode) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
        }
        
        if ($id = $request->get('id', 0)) {
            $node = $service->getNode($id);
            if (!$node) {
                throw $this->createNotFoundException('Invalid parent breadcrumb');
            }
            $form = $this->createForm(new BreadcrumbFormType(), $node);
        }
        else {
            $form = $this->createForm(new BreadcrumbFormType(), new BreadcrumbTree());
        }
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            
            $node = $form->getData();
            if ($parentNode) {
                $node->setParent($parentNode);
            }
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($node);
            $em->flush();
            
            $request->getSession()->setFlash('success', 'Successfully saved breadcrumb '.$node->getLabel());
        }
        else {
            $request->getSession()->setFlash('error', 'Missing required fields in form');
        }
        $referer = $request->getSession()->get('referer', $this->generateUrl('helper_utility_adminBreadcrumbs'));
        $request->getSession()->remove('referer');
        
        return $this->redirect($referer);
    }
}