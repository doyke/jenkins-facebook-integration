<?php

namespace FHJ\Controllers;

use Silex\Application;
use Monolog\Logger;
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

}
