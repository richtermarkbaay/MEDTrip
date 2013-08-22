<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService;

use HealthCareAbroad\SearchBundle\Form\FilterFormType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilder;
use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\MedicalCenterListType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\SearchBundle\Constants;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\SearchBundle\Form\AdminDefaultSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function showWidgetAction($context)
    {
        if ('admin' == $context) {
            $form = $this->createForm(new AdminDefaultSearchType());

            return $this->render('SearchBundle:Admin:searchWidget.html.twig', array('form' => $form->createView()));
        } else {
            throw new \Exception('Undefined context.');
        }
    }

    /**
     * Main search function
     */
    public function initiateAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $searchCriteria = $request->get('adminDefaultSearch', array());
        } else {
            $searchCriteria['category'] = $request->get('category');
            $searchCriteria['term'] = trim($request->get('term', ''));
        }

        $searchCriteria['page'] = $request->get('page', 1);

        switch ($searchCriteria['category']) {
            case Constants::SEARCH_CATEGORY_INSTITUTION:
                $categoryName = 'Institutions';
                break;
            case Constants::SEARCH_CATEGORY_CENTER:
                $categoryName = 'Medical Centers';
                break;
            case Constants::SEARCH_CATEGORY_PROCEDURE_TYPE:
                $categoryName = 'Treatments';
                break;
            case Constants::SEARCH_CATEGORY_DOCTOR:
                $categoryName = 'Doctors';
                break;
            case Constants::SEARCH_CATEGORY_SPECIALIZATION:
                $categoryName = 'Specializations';
                break;
            case Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION:
                $categoryName = 'Sub-specializations';
                break;
            default:
                $categoryName = '';
        }

        $p = new SearchAdminPagerService();

        return $this->render('SearchBundle:Admin:searchResult.html.twig', array(
            "data" => $this->get('services.admin_search')->search($searchCriteria, $p),
            "pager" => $p->getPager(),
            "isDoctor" => Constants::SEARCH_CATEGORY_DOCTOR == $searchCriteria['category'],
            "category" => $searchCriteria['category'],
            "categoryName" => $categoryName,
            "term" => trim($searchCriteria['term'])
        ));
    }
}
