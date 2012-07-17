<?php

namespace HealthCareAbroad\UserBundle\Controller;

use ChromediaUtilities\Helpers\SecurityHelper;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProviderUserController extends Controller
{
    public function loginAction()
    {
        $user = new ProviderUser();
        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('property_path'=> false))
            ->add('password', 'password', array('property_path'=> false))
            ->getForm();
        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            
            $form->bindRequest($request);
            if ($form->isValid()) {
                
                $user->setEmail($form->get('email')->getData());
                $user->setPassword(SecurityHelper::hash_sha256($form->get('password')->getData()));
                $user = $this->get('services.provider_user')->findByEmailAndPassword($user->getEmail(), $user->getPassword());
                
                if (!$user){
                    // invalid credentials
                    $this->get('session')->setFlash('flash.notice', 'Email and Password is invalid.');
                    
                    return $this->redirect($this->generateUrl('provider_user.login'));
                }
                else {
                    
                    $this->get('session')->setFlash('flash.notice', 'Login successfully!');
                    $token = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'provider_secured_area', array('ROLE_ADMIN'));
                    $this->get("security.context")->setToken($token);
                    $this->getRequest()->getSession()->set('_security_provider_secured_area',  \serialize($token));
                    
                    return $this->redirect($this->generateUrl('provider_homepage'));
                }
            }
        }
        return $this->render('UserBundle:ProviderUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('main_homepage'));
    }
    
}