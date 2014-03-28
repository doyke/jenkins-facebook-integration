<?php

namespace FHJ\Facebook\Data;

use Monolog\Logger;

/**
 * FacebookDataRetriever
 * @package FHJ\Facebook\Data
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
		$this->facebook->setExtendedAccessToken();
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
		$this->logger->addInfo(sprintf('facebook data retriever: getGroups for uid "%s"', $this->getUserId()));

		if ($this->groups === null) {
			$groups = $this->callFacebookApi('/me/groups');

			$this->logger->addDebug(sprintf('facebook data retriever: getGroups: got groups for uid "%s"',
					$this->getUserId()), array('groups' => $groups));

			$this->groups = array();
			if (count($groups['data'])) {
				foreach ($groups['data'] as $group) {
					$this->groups[$group['id']] = $group['name'];
				}
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