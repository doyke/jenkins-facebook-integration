<?php


namespace FHJ\Framework;

use Silex\Application;
use Silex\Route;
use Silex\Route\SecurityTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FHJ\Entities\User;

/**
 * SecuredRoute
 * @package FHJ\Framework
 */
class SecuredRoute extends Route {

	// this provides us the secure() method for restricting a controller method to certain user roles
	use SecurityTrait;

    public function onlyIfProjectsAllowed() {
        $this->before(function (Request $request, Application $app) {
	        $app['monolog']->addInfo('Running security check: onlyIfProjectsAllowed');

	        // If we do not have a token, we do not have an authenticated user
	        if ($app['security']->getToken() === null) {
		        throw new AccessDeniedException('Access denied, token is null');
	        }

            $user = $app['security']->getToken()->getUser();
        
            // If we don't have any user or any user of our entity type, fail
            if ($user === null || !$user instanceof User) {
                throw new AccessDeniedException(sprintf('Access denied, user is not valid (user type is "%s")',
	                gettype($user)));
            }
            
            if (!$user->isProjectCreationAllowed()) {
                $app['monolog']->addWarning(sprintf('user "%d" not allowed to create projects',
                    $user->getId()));

                throw new AccessDeniedException('Access denied, project creation not allowed');
            }
        });
    }
    
    public function onlyProjectAllowAccessIfAdminOrOwner() {
        $this->before(function (Request $request, Application $app) {
	        $app['monolog']->addInfo('Running security check: onlyProjectAllowAccessIfAdminOrOwner');

	        // If we do not have a token, we do not have an authenticated user
	        if ($app['security']->getToken() === null) {
		        throw new AccessDeniedException('Access denied, token is null');
	        }

	        $user = $app['security']->getToken()->getUser();
        
            // If we don't have any user or any user of our entity type, fail
            if ($user === null || !$user instanceof User) {
	            throw new AccessDeniedException(sprintf('Access denied, user is not valid (user type is "%s")',
		            gettype($user)));
            }

            if ($app['security']->isGranted(array('ROLE_ADMIN'))) {
                return;
            }

	        $projectId = $request->attributes->get('project');
	        $project = $app['repository.projects']->findProjectById(intval($projectId));
            if ($project === null || $project->getUserId() === $user->getId()) {
                return;
            }

            $app['monolog']->addWarning(sprintf(
                    'user "%d" not allowed to access project "%d" (missing ROLE_ADMIN and not owner)',
                    $user->getId(), $projectId));

	        throw new AccessDeniedException('Access denied, not allowed to view project');
        });
    }

} 