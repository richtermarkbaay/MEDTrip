<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\UserBundle\Form\AdminUserFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContext;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

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
//     public function loginAction()
//     {
//         $form = $this->createForm(new UserLoginType());
//         if ($this->getRequest()->isMethod('POST')) {
//             $form->bindRequest($this->getRequest());

//             if ($form->isValid()) {
//                 if ($this->get('services.admin_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
//                     // valid login
//                     $this->get('session')->setFlash('success', 'Login successfully!');

//                     return $this->redirect($this->generateUrl('admin_homepage'));
//                 }
//                 else {
//                     // invalid login
//                     $this->get('session')->setFlash('notice', 'Either your email or password is wrong.');
//                 }
//             }
//             else {
//                 $this->get('session')->setFlash('notice', 'Email and password are required.');
//             }
//         }

//         return $this->render('AdminBundle:AdminUser:login.html.twig', array(
//             'form' => $form->createView(),
//         ));
//     }

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('AdminBundle:AdminUser:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }

//     public function logoutAction()
//     {
//         $this->get('security.context')->setToken(null);
//         $this->getRequest()->getSession()->invalidate();
//         return $this->redirect($this->generateUrl('admin_login'));
//     }
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
        $form = $this->createForm(new AdminUserFormType(), $adminUser);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bindRequest($this->getRequest());

            if($form->isValid()) {

                $user = $this->get('services.admin_user')->update($adminUser);

                if ($user) {
                    $this->get('session')->setFlash('success', "Successfully updated account");

                    // dispatch event
                    $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_ADMIN_USER, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_ADMIN_USER, $adminUser));
                } else {
                    $this->get('session')->setFlash('error', "Unable to update account");
                }

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

                $adminUser->setPassword(SecurityHelper::hash_sha256($form->get('new_password')->getData()));
                $adminUser = $this->get('services.admin_user')->update($adminUser);

                // dispatch event
                $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_CHANGE_PASSWORD_ADMIN_USER, $this->get('events.factory')->create(AdminBundleEvents::ON_CHANGE_PASSWORD_ADMIN_USER, $adminUser));

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
        //$users = $this->filteredResult;
        $users = $this->get('services.admin_user')->getActiveUsers();
 
        return $this->render('AdminBundle:AdminUser:index.html.twig', array(
            'users' => $users
        ));
    }
    
    /**
     * Create a new admin user
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $adminUser = new AdminUser();
        $form = $this->createForm(new AdminUserFormType(), $adminUser);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $adminUser->setPassword($this->get('services.admin_user')->encryptPassword($adminUser->getPassword()));
                $this->get('services.admin_user')->create($adminUser);
                $this->get('session')->setFlash('success', "User successfully created.");
                
                return $this->redirect($this->generateUrl('admin_user_index'));
            }
        }
        
        return $this->render('AdminBundle:AdminUser:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}