<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\UserBundle\Form\UserAccountDetailType;
use HealthCareAbroad\UserBundle\Form\UserLoginType;
use HealthCareAbroad\UserBundle\Form\AdminUserChangePasswordType;
use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AdminUserController extends Controller
{
    public function loginAction()
    {
        $form = $this->createForm(new UserLoginType());
        if ($this->getRequest()->isMethod('POST')) {
            $form->bindRequest($this->getRequest());
            
            if ($form->isValid()) {
                if ($this->get('services.admin_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
                    // valid login
                    $this->get('session')->setFlash('success', 'Login successfully!');
            
                    return $this->redirect($this->generateUrl('admin_homepage'));
                }
                else {
                    // invalid login
                    $this->get('session')->setFlash('notice', 'Either your email or password is wrong.');
                }
            }
            else {
                $this->get('session')->setFlash('notice', 'Email and password are required.');
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
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * 
     */
    public function editAccountAction()
    {
    	$accountId = $this->getRequest()->getSession()->get('accountId');

    	//get user's data
    	$adminUser = $this->get('services.admin_user')->findById($accountId, true); //get user account in chromedia global accounts by accountID
        if (!$adminUser) {
            throw $this->createNotFoundException('Cannot update invalid account.');
        }
    	$form = $this->createForm(new UserAccountDetailType(), $adminUser);
    	
    	if ($this->getRequest()->isMethod('POST')) {
			$form->bindRequest($this->getRequest());
			
	    	if($form->isValid()) {
	    		//TODO:: persist data to database
	    		$user = $this->get('services.admin_user')->update($adminUser);
	    		if(!$user) {
	    			//TODO:: send notification to hca admin
	    			$this->get('session')->setFlash('error', "Unable to update account");
	    			 
	    		}
	    		$this->get('session')->setFlash('success', "Successfully updated account");
	    		
	    	}
    	}
    	return $this->render('AdminBundle:AdminUser:edit.html.twig', array(
    			'form' => $form->createView(),
    			'user' => $adminUser
    			));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     *
     */
    public function changePasswordAction()
    {
    	$accountId = $this->getRequest()->getSession()->get('accountId');
    	
    	//get user's data
    	$adminUser = $this->get('services.admin_user')->findById($accountId, true);
    	if(!$adminUser) {
    		throw $this->createNotFoundException('Cannot update invalid account');
    	}
    	
    	$form = $this->createForm(new AdminUserChangePasswordType(), $adminUser);
    	
    	if ($this->getRequest()->isMethod('POST')) {
	    	$form->bindRequest($this->getRequest());
	    	
	    	if($form->isValid()) {
	    		//TODO:: persist new password to db
	    		$adminUser->setPassword(SecurityHelper::hash_sha256($form->get('new_password')->getData()));
	    		$adminUser = $this->get('services.admin_user')->update($adminUser);
	    		
	    		$this->get('session')->setFlash('success', "Password changed!");
	    		return $this->redirect($this->generateUrl('admin_homepage'));
	    		
	    	}
    	}
    	return $this->render('AdminBundle:AdminUser:changePassword.html.twig', array(
    			'form' => $form->createView(),
    			));
    }
    /**
     * View all admin users
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function indexAction()
    {
        $users = $this->get('services.admin_user')->getActiveUsers();
        return $this->render('AdminBundle:AdminUser:index.html.twig', array(
            'users' => $users    
        ));
    }
}