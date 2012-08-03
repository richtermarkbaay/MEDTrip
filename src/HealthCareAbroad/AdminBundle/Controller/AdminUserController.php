<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminUserController extends Controller
{
    public function loginAction()
    {
        $user = new AdminUser();
        $form = $this->createFormBuilder($user)
        ->add('email', 'email', array('property_path'=> false))
        ->add('password', 'password', array('property_path'=> false))
        ->getForm();
        
        if ($this->getRequest()->isMethod('POST')) {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                if ($this->get('services.admin_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
                    // valid login
                    $this->get('session')->setFlash('notice', 'Login successfully!');
            
                    return $this->redirect($this->generateUrl('admin_homepage'));
                }
                else {
                    // invalid login
                    $this->get('session')->setFlash('notice', 'Email and Password is invalid.');
            
                    return $this->redirect($this->generateUrl('admin_login'));
                }
            }
        }
        
        return $this->render('AdminBundle:AdminUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('admin_login'));
    }
}