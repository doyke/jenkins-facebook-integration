<?php


namespace FHJ\Framework;

use Silex\Application;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
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
        $this->before(function (Application $app) {
            $user = $app['security']->getUser();
        
            // If we don't have any user or any user of our entity type, fail
            if ($user === null || !$user instanceof User) {
                throw new AccessDeniedException();
            }
            
            if (!$user->isProjectCreationAllowed()) {
                $app['monolog']->addWarning(sprintf('user "%d" not allowed to create projects',
                    $user->getId()));

                throw new AccessDeniedException();
            }
        });
    }
    
    public function onlyProjectAllowAccessIfAdminOrOwner() {
        $this->before(function (Request $request, Application $app) {
            $user = $app['security']->getUser();
        
            // If we don't have any user or any user of our entity type, fail
            if ($user === null || !$user instanceof User) {
                throw new AccessDeniedException();
            }
            
            $projectId = $request->attributes->get('project');
            
            if ($app['security']->isGranted(array('ROLE_ADMIN'))) {
                return;
            }
            
            $project = $app['repository.projects']->findProjectById(intval($projectId));
            if ($project === null || $project->getUserId() === $user->getId()) {
                return;
            }

            $app['monolog']->addWarning(sprintf(
                    'user "%d" not allowed to access project "%d" (missing ROLE_ADMIN and not owner)',
                    $user->getId(), $projectId));
            
            throw new AccessDeniedException();
        });
    }

} 