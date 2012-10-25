<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

/**
 * Dummy service class.
 *
 * Refactor: Extract the needed functionality of the twig extension to
 * another class so we don't have use this class.
 */
class TwigUserService extends UserService
{
    public function login($email, $password) {}
    public function findByEmailAndPassword($email, $password) {}
    public function findById($id, $activeOnly = true) {}
    public function update(SiteUser $siteUser) {}
    public function create(SiteUser $siteUser) {}
    public function getAccountData(SiteUser $siteUser) {}
    public function setSessionVariables(SiteUser $user) {}
    public function getUserRolesForSecurityToken(SiteUser $user) {}
}