<?php
namespace HealthCareAbroad\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;
use HealthCareAbroad\HelperBundle\Services\ErrorValidationHelper;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\LogBundle\Entity\LogEventData;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
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
            
            try{
                $fromSpecializationOldData = array(
                	'id' => $fromSpecialization->getId(),
                    'name' => $fromSpecialization->getName()
                );
                
                $this->get('services.admin.treatmentMigrationTool')
                    ->migrateSpecializationToAnotherSpecialization($fromSpecialization, $toSpecialization);
                
                $eventData = new LogEventData();
                $eventData->setMessage(sprintf("Migrated %s to %s", $fromSpecializationOldData['name'], $toSpecialization->getName()));
                $eventData->setData(array(
                	'fromSpecialization' => $fromSpecializationOldData['id'],
                    'toSpecialization' => $toSpecialization->getId()
                ));
                
                $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_ADMIN_MIGRATE_SPECIALIZATION, $this->get('events.factory')->create(AdminBundleEvents::ON_ADMIN_MIGRATE_SPECIALIZATION, $eventData));
                
                $responseData = array(
                    'toSpecializationId' => $toSpecialization->getId(),
                    'redirectUrl' => $this->generateUrl('admin_specialization_manage', array('id' => $toSpecialization->getId()))
                );
                $responseStatus = 200;
            }
            catch (\Exception $e) {
                $responseStatus = 500;
                $responseData = array('error' => $e->getMessage());
            }
            
            $response = new Response(\json_encode($responseData), $responseStatus,array('content-type' => 'application/json'));
        }
        else {
            $errors = array();
            ErrorValidationHelper::processFormErrorsDeeply($tokenForm, $errors);
            
            $response = new Response(\json_encode(array('errors' => $errors)), 400, array('content-type' => 'application/json'));
        }
        
        return $response;
    }
    
    /**
     * View merge treatment tool
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewMergeTreatmentAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('sp')
            ->from('TreatmentBundle:Specialization', 'sp')
            ->orderBy('sp.name', 'ASC');
        
        $tokenForm = $this->buildTokenForm();
        
        $specializations = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $specializationsJSON = \json_encode($specializations);
        
        return $this->render('AdminBundle:MigrationTools:viewMergeTreatment.html.twig', array(
        	'specializationsJSON' => $specializationsJSON,
            'tokenForm' => $tokenForm->createView()
        ));
    }
    
    public function processMergeTreatmentAction(Request $request)
    {
        $tokenForm = $this->buildTokenForm();
        $tokenForm->bind(array('_token' => $request->get('_token')));
        if ($tokenForm->isValid()) {
        	$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($request->get('specialization', 0));
        	if (!$specialization) {
        		throw $this->createNotFoundException('Invalid specialization');
        	}
        	
        	$treatmentRepo = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment');
        	$fromTreatment = $treatmentRepo->find($request->get('fromTreatment', 0));
        	$toTreatment = $treatmentRepo->find($request->get('toTreatment', 0));
        	
        	if (!$fromTreatment) {
        	    throw $this->createNotFoundException('Invalid `from treatment`');
        	}
        	
        	if (!$toTreatment) {
        	    throw $this->createNotFoundException('Invalid `to treatment`');
        	}
        	
        	// check that both treatments belong to $specialization
        	if ($fromTreatment->getSpecialization()->getId() != $specialization->getId()) {
        	    throw $this->createNotFoundException('From treatment must belong to Specialization '.$specialization->getId());
        	}
        	
        	if ($toTreatment->getSpecialization()->getId() != $specialization->getId()) {
        	    throw $this->createNotFoundException('To treatment must belong to Specialization '.$specialization->getId());
        	}
        	
        	try {
        	    $oldFromTreatmentData = array(
    	            'id' => $fromTreatment->getId(),
    	            'name' => $fromTreatment->getName()
        	    );
        	    
        	    // DO THE MERGING HERE
        	    $this->get('services.admin.treatmentMigrationTool')
        	       ->mergeTreatmentToAnotherTreatment($fromTreatment, $toTreatment);
        	    
        	    $eventData = new LogEventData();
        	    $eventData->setMessage(\sprintf("Merged %s to %s", $oldFromTreatmentData['name'], $toTreatment->getName()));
        	    $eventData->setData(array(
    	            'fromTreatment' => $oldFromTreatmentData,
    	            'toTreatment' => array('id' => $toTreatment->getId(), 'name' => $toTreatment->getName())
        	    ));
        	    
        	    // FIRE EVENT
        	    $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_ADMIN_MERGE_TREATMENT, $this->get('events.factory')->create(AdminBundleEvents::ON_ADMIN_MERGE_TREATMENT, $eventData));
        	    
        	    $responseStatus = 200;
        	    $responseData = array(
                    'toTreatmentId' => $toTreatment->getId(),
                    'redirectUrl' => $this->generateUrl('admin_specialization_manage', array('id' => $specialization->getId()))
                );
        	}
        	catch (\Exception $e){
        	    $responseStatus = 500;
        	    $responseData = array('error' => $e->getMessage());
        	}
        	
        	$response = new Response(\json_encode($responseData), $responseStatus,array('content-type' => 'application/json'));
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