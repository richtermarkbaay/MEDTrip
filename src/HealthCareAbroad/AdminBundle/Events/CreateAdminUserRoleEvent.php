<?php 

namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\UserBundle\Entity\AdminUserRole;

use Symfony\Component\EventDispatcher\Event;


class CreateAdminUserRoleEvent extends Event{
	
	protected $adminUserRole;
	
	public function __construct(AdminUserRole $roles)
	{
		$this->adminUserRole = $roles;
	}
	
	public function getAdminUserRole()
	{
		return $this->adminUserRole;
	}
}
