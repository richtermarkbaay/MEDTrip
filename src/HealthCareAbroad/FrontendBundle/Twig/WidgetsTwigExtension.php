<?php

namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber;

use HealthCareAbroad\FrontendBundle\Form\NewsletterSubscriberFormType;

use HealthCareAbroad\HelperBundle\Entity\FeedbackMessage;

use HealthCareAbroad\HelperBundle\Form\FeedbackMessageFormType;

use Symfony\Component\Form\FormFactory;

class WidgetsTwigExtension extends \Twig_Extension
{
    /**
     * @var FormFactory
     */
    private $formFactory;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    public function getName()
    {
        return 'frontend_widgets_twig_extension';
    }
    
    public function setFormFactory(FormFactory $v)
    {
        $this->formFactory = $v;
    }
    
    public function setTwig(\Twig_Environment $v)
    {
        $this->twig = $v;
    }
    
    public function getFunctions()
    {
        return array(
            'render_feedback_form' => new \Twig_Function_Method($this, 'render_feedback_form'),
            'render_newsletter_subscription_form' => new \Twig_Function_Method($this, 'render_newsletter_subscription_form'),
        );
    }
    
    public function render_feedback_form()
    {
        $form = $this->formFactory->create(new FeedbackMessageFormType(), new FeedbackMessage());
        
        return $this->twig->render('FrontendBundle:Embed:modal.feedbackMessage.html.twig', array(
            'feedbackForm' => $form->createView()
        ));
    }
    
    public function render_newsletter_subscription_form()
    {
        $form = $this->formFactory->create(new NewsletterSubscriberFormType(), new NewsletterSubscriber());
        
        return $this->twig->render('FrontendBundle:Widgets:footer.subscribeNewsletter.html.twig', array(
                        'subscribeNewsletterForm' => $form->createView()
        ));
    }
}