<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\News;
use HealthCareAbroad\HelperBundle\Form\NewsFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class NewsController extends Controller
{
    /**
     * View All News
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_NEWS')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:News:index.html.twig', array('news' => $this->filteredResult, 'pager' => $this->pager));
    }

    /**
     * Add News
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_NEWS')")
     */
    public function addAction()
    {
        $form = $this->createForm(new NewsFormType(), new News());

        return $this->render('AdminBundle:News:form.html.twig', array(
                'id' => null,
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_news_create')
        ));
    }
    
    /**
     * Edit News
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_NEWS')")
     */
    public function editAction($id)
    {
        $news = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:News')->find($id);
         
        $form = $this->createForm(new NewsFormType(), $news);
    
        return $this->render('AdminBundle:News:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_news_update', array('id' => $id))
        ));
    }
    
    /**
     * Save added News
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_NEWS')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
    
        if('POST' != $request->getMethod()) {
        	return new Response("Save requires POST method!", 405);
        }
        
        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();    
        $news = $id ? $em->getRepository('HelperBundle:News')->find($id) : new News();    
        $form = $this->createForm(new NewsFormType(), $news);
        $form->bind($request);
    
        if ($form->isValid()) {
            
            $em->persist($news);
            $em->flush($news);
    
            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_NEWS : AdminBundleEvents::ON_ADD_NEWS;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $news));
                
            $request->getSession()->setFlash('success', 'News has been saved!');
            
            return $this->redirect($this->generateUrl('admin_news_index'));                            
        }
    
        $formAction = $id ? $this->generateUrl('admin_news_update', array('id' => $id)) : $this->generateUrl('admin_news_create');
    
        return $this->render('AdminBundle:News:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction
        ));
    }
    
    /**
     * Delete News / Update status into INACTIVE
     * 
     */
    public function updateStatusAction($id)
    {

        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $news = $em->getRepository('HelperBundle:News')->find($id);

        if ($news) {
            $news->setStatus($news->getStatus() ? 0 : 1);
                    
            $em->persist($news);
            $em->flush($news);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_NEWS, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_NEWS, $news));
            
            $result = true;
        }
    
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
    
    /**
     * Show a news entry
     */
//     public function showAction($id)
//     {
//     	$news = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:News')->find($id);
// 		print_r($news);exit;
//         if (!$news) {
//             throw $this->createNotFoundException('Unable to find News post.');
//         }
        
//         return $this->render('InstitutionBundle:News:index.html.twig', array(
//             'news'      => $news
//         ));
//     }

}