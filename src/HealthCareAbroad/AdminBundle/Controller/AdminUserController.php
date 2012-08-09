<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\UserBundle\Form\AdminUserFormType;

use HealthCareAbroad\UserBundle\Form\UserLoginType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
                    $this->get('session')->setFlash('notice', 'Login successfully!');
            
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
    
    public function addAction()
    {
        $adminUser = new AdminUser();
        $form = $this->createForm(new AdminUserFormType(), $adminUser);
        
        return $this->render('AdminBundle:AdminUser:form.html.twig', array(
            'form' => $form->createView(),
            'user' => $adminUser
        ));
    }
    
    public function editAction()
    {
        
    }
    
    public function saveAction()
    {
        
    }
}