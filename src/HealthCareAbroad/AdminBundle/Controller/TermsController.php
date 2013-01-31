<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermsController extends Controller
{
    public function indexAction()
    {
        $terms = $this->getDoctrine()->getEntityManager()->getRepository('TermBundle:Term')->findAll();
        $data = array('terms'=>$terms);
        return $this->render('AdminBundle:Terms:index.html.twig', $data);
    }
    
    public function ajaxListAction(Request $request)
    {
        $documentId = $request->get('documentId', 0);
        $documentType = $request->get('documentType', 0);
        $terms = $this->get('services.terms')->findByDocumentIdAndType($documentId, $documentType, true);
        $html = $this->renderView('AdminBundle:Terms:selectedTermContainer.html.twig', array('terms' => $terms));
        
        return new Response(\json_encode(array('html' => $html)), '200', array('content-type' => 'application/json'));
    }
    
    /**
     * Delete term document
     * @param Request $request
     */
    public function ajaxDeleteByDocumentIdAction(Request $request)
    {
        $documentId = $request->get('documentId', 0);
        $documentType = $request->get('documentType', 0);
        $termId = $request->get('termId', 0);
        
        $termDocument = $this->getDoctrine()->getRepository('TermBundle:TermDocument')
            ->findOneBy(array('documentId' => $documentId, 'type' => $documentType, 'term' => $termId));
        
        if (!$termDocument) {
            throw $this->createNotFoundException('Invalid TermDocument');
        }
        
        $documentObject = $this->get('services.terms')->createDocumentObject($termDocument);
        if (!$documentObject) {
            // why arrive at this action if the subject document does not exist?
            throw $this->createNotFoundException('Target document object does not exist');
        }
        
        // extra check that term name should not be equal to document object's name since we do not allow to modify automatically tagged from document object name
        if ($documentObject->getName() == $termDocument->getTerm()->getName()) {
            $response = new Response('Cannot delete term document pointing to the automatically tagged term from document object name', 400);
        }else {
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($termDocument);
                $em->flush();
                
                $response = new Response('Term document deleted', 200);
            }
            catch(\Exception $e) {
                $response = new Response($e->getMessage(), 500);
            }
        }
        
        return $response;
    }
    
    public function loadAutocompleteSourceAction(Request $request)
    {
        $type = $request->get('type', 3);
        $term = \trim($request->get('term', ''));
        $terms = array();
        
        if ('' != $term) {
            $result = $this->get('services.terms')->findByName($term, 20);
            foreach ($result as $_each) {
                $html = '<span data-termId="'.$_each->getId().'" style="border: 1px inset; padding: 3px 5px; margin-right: 3px; cursor: pointer;" class="autocompleteSelected">'.$_each->getName().'</span>';
                $terms[] = array(
                    'id' => $_each->getId(),
                    //'value' => $_each->getId(),
                    'label' => $_each->getName(),
                    'html' => $html
                );
            }
        }
        
        return new Response(\json_encode($terms), 200, array('content-type' => 'application/json'));
    }
       
}