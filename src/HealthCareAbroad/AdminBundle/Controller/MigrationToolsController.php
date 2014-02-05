<?php
namespace HealthCareAbroad\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;
use HealthCareAbroad\HelperBundle\Services\ErrorValidationHelper;
use Symfony\Component\HttpFoundation\Response;
class MigrationToolsController extends Controller
{
    public function viewSpecializationMigrationAction()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('sp')
            ->from('TreatmentBundle:Specialization', 'sp')
            ->orderBy('sp.name', 'ASC');
        
        $tokenForm = $this->buildTokenForm();
        
        $specializations = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $specializationsJSON = \json_encode($specializations);
        
        return $this->render('AdminBundle:MigrationTools:viewSpecializations.html.twig', array(
        	'specializationsJSON' => $specializationsJSON,
            'tokenForm' => $tokenForm->createView()
        ));
    }
    
    
    /**
     * Process migration.
     * 
     * This is very unsafe operation!!!
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function processSpecializationMigrationAction(Request $request)
    {
        $tokenForm = $this->buildTokenForm();
        $tokenForm->bind(array('_token' => $request->get('_token')));
        // validate CSRF token
        if ($tokenForm->isValid()) {
            $specializationRepo = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization');
            
            $fromSpecialization = $specializationRepo->find($request->get('fromSpecialization', 0));
            $toSpecialization = $specializationRepo->find($request->get('toSpecialization', 0));
            
            if (!$fromSpecialization) {
                throw $this->createNotFoundException('Invalid subject specialization');
            }
            
            if (!$toSpecialization){
                throw $this->createNotFoundException('Invalid target specialization');
            }
            
            /***
             * Steps for migration
             * 1. 
             */
            
            
            
            
            $response = new Response(
                \json_encode(array(
                    'targetSpecialization' => $toSpecialization->getId()
                )), 
                200, 
                array('content-type' => 'application/json')
            );
        }
        else {
            $errors = array();
            ErrorValidationHelper::processFormErrorsDeeply($tokenForm, $errors);
            
            $response = new Response(\json_encode(array('errors' => $errors)), 400, array('content-type' => 'application/json'));
        }
        
        return $response;
    }
    
    
    /**
     * 
     * @return \Symfony\Component\Form\Form
     */
    private function buildTokenForm()
    {
        $form = $this->createFormBuilder()->getForm();
        
        return $form;
    }
}