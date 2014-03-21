<?php

namespace FHJ\Facebook;

use Monolog\Logger;

/**
 * FacebookDataRetriever
 * @package FHJ\Facebook
 */
class FacebookDataRetriever {

	/**
	 * @var \BaseFacebook
	 */
	private $facebook;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var array|null
	 */
	private $userData = null;

	/**
	 * @var array|null
	 */
	private $groups = null;

	/**
	 * @param \BaseFacebook $facebook
	 * @param Logger $logger
	 */
	public function __construct(\BaseFacebook $facebook, Logger $logger) {
		$this->facebook = $facebook;
		$this->logger = $logger;
	}

	public function getUserId() {
		$uid = $this->facebook->getUser();
		if ($uid === 0) {
			return null;
		}

		return $uid;
	}

	public function getAccessToken() {
		return $this->facebook->getAccessToken();
	}

	public function getEmail() {
		$this->retrieveUserData();
		return $this->userData['email'];
	}

	public function getRealname() {
		$this->retrieveUserData();
		return $this->userData['name'];
	}

	public function getGroups() {
		if ($this->groups === null) {
			$groups = $this->callFacebookApi('/me/groups');

			$this->logger->addDebug(sprintf('FacebookDataRetriever.getGroups for uid "%s"', $this->getUserId()),
				array($groups));

			$this->groups = array();
			foreach ($groups as $group) {
				$this->groups[$group['id']] = $group['name'];
			}
		}

		return $this->groups;
	}

	private function retrieveUserData() {
		if ($this->userData === null) {
			$this->userData = $this->callFacebookApi('/me');
		}
	}

	private function callFacebookApi($what, $extraParameters = array()) {
		return $this->facebook->api($what, 'GET', $extraParameters);
	}
} 