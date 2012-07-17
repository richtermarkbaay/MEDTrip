<?php
namespace HealthCareAbroad\UserBundle\Controller;

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
        return $this->render('UserBundle:AdminUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}