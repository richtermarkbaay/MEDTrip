<?php

namespace HealthCareAbroad\HelperBundle\Controller;


use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function apiUtilityJavascriptAction()
    {
        $response = $this->render('HelperBundle:Default:apiUtility.js.twig');
        $response->headers->set('content-type', 'application/javascript');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('no-store', true);
        
        return $response;
    }
    
    public function error403Action()
    {
        return $this->render('HelperBundle:Default:error403.html.twig');
    }
    
    public function indexAction($name)
    {
        return $this->render('HelperBundle:Default:index.html.twig', array('name' => $name));
    }

    
    public function loadCitiesAction(Request $request)
    {
        $countryId = $request->get('countryId', 0);

        if($request->get('loadNonGlobalCities')) {
            $data = $this->get('services.location')->getListActiveCitiesByCountryId($countryId);
        } else {
            $data = $this->get('services.location')->getGlobalCities(array('country_id' => $countryId, 'key_value' => 1));
        }

        $response = new Response(json_encode(array('data' => $data)), 200, array('content-type' => 'application/json'));

        return $response;
    }
    
    /**
     * Get accordion form for specialization
     * 
     * @param unknown_type $specializationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSpecializationAccordionEntryAction(Request $request)
    {
        $specializationId = $request->get('specializationId', 0);
        
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);

        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);

        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');

            return new Response('Invalid Specialization', 404);
        }

        $groupBySubSpecialization = true;
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
        $params['formName'] = InstitutionSpecializationFormType::NAME;
        $params['form'] = $form->createView();
        $params['subSpecializations'] = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getBySpecializationId($specializationId, $groupBySubSpecialization);
        $params['showCloseBtn'] = $this->getRequest()->get('showCloseBtn', true);
        $params['selectedTreatments'] = $this->getRequest()->get('selectedTreatments', array());

        $html = $this->renderView('HelperBundle:Widgets:specializationAccordionEntry.html.twig', $params);
//         $html = $this->renderView('HelperBundle:Widgets:testForm.html.twig', $params);
        //echo $html; exit;
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
    private function _rename($sqlTableName, $tableName)
    {
        echo "<br />{$sqlTableName} ================ <br /><br />";
        
        $sql = 'SELECT id FROM `'.$sqlTableName.'` WHERE name NOT IN (SELECT name from terms) ORDER BY name ASC';
        $em = $this->getDoctrine()->getEntityManager();
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        $ids = array();
        
        while ($row = $statement->fetch(Query::HYDRATE_ARRAY)){
            $ids[] = $row['id'];
        }
        
        echo "FOUND ".\count($ids). " rows <br />";
        
        if (\count($ids) <=0 ) {
            return false;
        }
        
        $qb = $em->createQueryBuilder();
        $results = $qb->select('a')
            ->from($tableName, 'a')
            ->where($qb->expr()->in('a.id', ':ids'))
            ->setParameter('ids', $ids)
            ->orderBy('a.name', 'ASC')
            ->getQuery()->getResult();
        
        foreach ($results as $_obj) {
            $oldName = $_obj->getName();
            $newName = $oldName.time();
            echo "Renaming {$oldName} to {$newName} ... ";
        
            $_obj->setName($newName);
            $em->persist($_obj);
            $em->flush();
            echo "Reverting to {$oldName}";
        
            $_obj->setName($oldName);
                $em->persist($_obj);
                $em->flush();
        
                echo "<br />";
        }   
    }


    public function searchTagsAction()
    {
//         $this->_rename('treatments', 'TreatmentBundle:Treatment');
        
//         $this->_rename('sub_specializations', 'TreatmentBundle:SubSpecialization');
        
//         $this->_rename('specializations', 'TreatmentBundle:Specialization');
        
//         exit;
//         $data = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Tag')->searchTags($term);

//         $response = new Response(json_encode($data));
//         $response->headers->set('Content-Type', 'application/json');

//         return $response;
    }
    
    
    public function populateClinicDescriptionHighlightAction()
    {
        $allImcs = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')
            ->findAll();
        
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($allImcs as $imc) {
            echo "Stripping {$imc->getName()}... ";
            $highlight = \trim(\preg_replace('/&nbsp;/', ' ',\strip_tags($imc->getDescription())));
            // cut this to 200 chars only
            $imc->setDescriptionHighlight($highlight);
            $em->persist($imc);
            echo "<br/>";
        }
        $em->flush();
        exit;
    }
    
    
    // TODO: DEPRECATED ??
    public function autoCompleteSearchAction()
    {
        $data = array();
        $request = $this->getRequest();
        $section = $request->get('section', null);
        $term = $request->get('term', null);

        switch($section) {
            case 'specialization' :
                $data = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->autoCompleteSearch($term);
                break;
            case 'procedure-type' :
                $data = array(); // TODO - Get Array Result
                break;
            case 'procedure' :
                $data = array(); // TODO - Get Array Result
                break;
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');        
        return $response;
    }
}