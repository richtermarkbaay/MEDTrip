<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

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
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                
                $user->setEmail($form->get('email')->getData());
                $user->setPassword(SecurityHelper::hash_sha256($form->get('password')->getData()));
                $user = $this->get('services.provider_user')->findByEmailAndPassword($user->getEmail(), $user->getPassword());
                
                if (!$user){
                    // invalid credentials
                    $this->get('session')->setFlash('flash.notice', 'Email and Password is invalid.');
                    
                    return $this->redirect($this->generateUrl('provider_login'));
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
        return $this->render('ProviderBundle:ProviderUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('main_homepage'));
    }
    
    public function inviteAction()
    {
        $provider = $this->get('services.provider')->getCurrentProvider();
        
        $providerUserInvitation = new ProviderUserInvitation();
        $providerUserInvitation->setProvider($provider);
        $providerUserInvitation->setDateCreated(new \DateTime('now'));
        
        $form = $this->createFormBuilder($providerUserInvitation)
            ->add('email', 'email')
            ->add('message', 'textarea')
            ->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->getForm();
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            
            if ($form->isValid()){
                
                $sendingResult = $this->get('services.invitation')->sendProviderUserInvitation($provider, $providerUserInvitation);
                
                if ($sendingResult) {
                    $this->get('session')->setFlash('flash.notice', "Invitation sent to {$providerUserInvitation->getEmail()}");
                }
                else {
                    $this->get('session')->setFlash('flash.notice', "Failed to send invitation to {$providerUserInvitation->getEmail()}");
                }
                
                return $this->redirect($this->generateUrl('provider_invite_user'));
            }
        }
        
        return $this->render('ProviderBundle:Default:invite.html.twig', array(
                        'form' => $form->createView(),
        ));
    }
    
}