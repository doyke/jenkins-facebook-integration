<?php


namespace FHJ\Framework;

use Silex\Route;
use FHJ\Entities\User;

/**
 * SecuredRoute
 * @package FHJ\Framework
 */
class SecuredRoute extends Route {

	// this provides us the secure() method for restricting a controller method to certain user roles
	//use SecurityTrait;
	
	// copied from Silex\Route\SecurityTrait to provide compatibility for PHP 5.3
	public function secure($roles) {
        $this->before(function ($request, $app) use ($roles) {
            if (!$app['security']->isGranted($roles)) {
                throw new AccessDeniedException();
            }
        });
    }
    
    public function onlyIfProjectsAllowed() {
        $user = $app['security']->getUser();
        
        // If we don't have any user or any user of our entity type, fail
        if ($user === null || !$user instanceof User) {
            throw new AccessDeniedException();
        }
        
        if (!$user->isProjectCreationAllowed()) {
            throw new AccessDeniedException();
        }
    }

} 