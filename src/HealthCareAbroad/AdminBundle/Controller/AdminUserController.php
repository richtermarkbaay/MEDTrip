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
                $user->setEmail($form->get('email')->getData());
                $user->setPassword(SecurityHelper::hash_sha256($form->get('password')->getData()));
                $user = $this->get('services.admin_user')->findByEmailAndPassword($user->getEmail(), $user->getPassword());
                
                if (!$user) {
                    // invalid credentials
                    $this->get('session')->setFlash('flash.notice', 'Email and Password is invalid.');
                    
                    return $this->redirect($this->generateUrl('admin_login'));
                }
                else {
                    $this->get('session')->setFlash('flash.notice', 'Login successfully!');
                    $token = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'admin_secured_area', array('ROLE_ADMIN'));
                    $this->get("security.context")->setToken($token);
                    $this->getRequest()->getSession()->set('_security_admin_secured_area',  \serialize($token));
                    
                    return $this->redirect($this->generateUrl('admin_homepage'));
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