<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\News;
use HealthCareAbroad\HelperBundle\Form\NewsFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class NewsController extends Controller
{
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_NEWS')")
	 */
	public function indexAction()
	{
		return $this->render('AdminBundle:News:index.html.twig', array('news' => $this->filteredResult));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_NEWS')")
	 */
	public function addAction()
	{
		$form = $this->createForm(New NewsFormType(), new News());

		return $this->render('AdminBundle:News:form.html.twig', array(
				'id' => null,
				'form' => $form->createView(),
				'formAction' => $this->generateUrl('admin_news_create')
		));
	}

}