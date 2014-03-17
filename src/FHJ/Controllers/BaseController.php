<?php

namespace FHJ\Controllers;

use Silex\Application;
use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContextInterface;
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
	
	protected function getFacebookObject() {
	    return $this->application['facebook'];
	}

}
