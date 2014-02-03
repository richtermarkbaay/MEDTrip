<?php
namespace HealthCareAbroad\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;
use HealthCareAbroad\InstitutionBundle\Form\Api\InstitutionInquiryApiFormType;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionInquiryService;

class InstitutionInquiryApiController extends ApiController
{

    /**
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $knownFilters = array('status');
        $appliedFilters = array();
        foreach ($knownFilters as $filterName) {
            $filterValue = $request->get($filterName, null);
            if (null !== $filterValue) {
                $appliedFilters[$filterName] = $filterValue;
            }
        }

        $result = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')
            ->getResultByFilters($appliedFilters, Query::HYDRATE_ARRAY);

        $response = $this->createResponseAsJson(array('institutionInquiries' => $result), 200);

        return $response;
    }

    public function putEditAction(Request $request)
    {
        $institutionInquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')
            ->find($request->get('id', 0));

        if (!$institutionInquiry) {
            throw $this->createNotFoundException('Invalid institution inquiry');
        }

        $form = $this->createForm(new InstitutionInquiryApiFormType(), $institutionInquiry);
        $contentData = \json_decode($request->getContent(false), true);
        $formData = $contentData[$form->getName()];
        
        $oldStatus = $institutionInquiry->getStatus();
        $form->bind($formData);
        //$form->bind($this->getRequest());

        if ($form->isValid()) {
            
            
            $this->get('services.institution.inquiry')->save($institutionInquiry);
            
            $response = $this->createResponseAsJson(InstitutionInquiryService::toArray($institutionInquiry), 200);

            //Listener for NOTIFICATIONS_INQUIRIES events is configured to rethrow any exceptions encountered.
            try {
                // only fire mailer event if status was changed from unapproved
                if ($oldStatus == InstitutionInquiry::STATUS_UNAPPROVED && $institutionInquiry->getStatus() != InstitutionInquiry::STATUS_UNAPPROVED) {
                    $inquiry = $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_INQUIRIES, new GenericEvent($institutionInquiry));
                }
            } catch (\Exception $e) {
                //TODO: Mark this inquiry as having failed to send notifications
                //ignored for now
            }
        }
        else {
            $response = $this->createResponseFromFormErrors($form);
        }

        return $response;
    }

    public function deleteAction(Request $request)
    {
        $institutionInquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')
            ->find($request->get('id', 0));

        if (!$institutionInquiry) {
            throw $this->createNotFoundException('Invalid institution inquiry');
        }
        $oldId = $institutionInquiry->getId();

        $this->get('services.institution.inquiry')->delete($institutionInquiry);
        $response = $this->createResponseAsJson(array ('id' => $oldId), 200);

        return $response;
    }
}