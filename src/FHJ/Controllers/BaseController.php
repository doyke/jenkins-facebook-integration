<?php

namespace FHJ\Controllers;

use Silex\Application;
use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use FHJ\Repositories\UserDbRepositoryInterface;
use FHJ\Repositories\ProjectDbRepositoryInterface;

/**
 * BaseController
 * @package FHJ\Controllers
 */
class BaseController {

	private $application;

	public function __construct(Application $application) {
		$this->application = $application;
	}

	protected function isDebug() {
		return $this->application['debug'];
	}

	/**
	 * @return Logger
	 */
	protected function getLogger() {
		return $this->application['logger'];
	}

	/**
	 * @return UserDbRepositoryInterface
	 */
	protected function getUserRepository() {
		return $this->application['repository.users'];
	}

	/**
	 * @return ProjectDbRepositoryInterface
	 */
	protected function getProjectRepository() {
		return $this->application['repository.projects'];
	}
	
	/**
	 * @return SecurityContextInterface
	 */ 
	protected function getSecurity() {
	    return $this->application['security'];
	}
	
	/**
	 * @return Cache
	 */
	protected function getCache() {
	    return $this->application['cache'];
	}
	
	protected function getTemplateEngine() {
	    return $this->application['twig'];
	}

	/**
	 * @return \BaseFacebook
	 */
	protected function getFacebookObject() {
	    return $this->application['facebook'];
	}
	
	/**
	 * @return SessionInterface
	 */ 
	protected function getSession() {
	    return $this->application['session'];
	}
	
	protected function generateRoute($destination, array $parameters = array()) {
	    return $this->application['url_generator']->generate($destination, $parameters);
	}
	
	/**
	 * Redirects an user to $destination. $destination must be the route name.
	 * 
	 * @param string $destination The route name for redirection
	 * @param array $parameters Additional route parameters like e.g. an id
	 */ 
	protected function doRedirect($destination, array $parameters = array()) {
	    $this->application->redirect($this->generateRoute($destination, $parameters));
	}

}
