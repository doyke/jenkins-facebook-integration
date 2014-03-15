<?php

namespace FHJ\Facebook;

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
	 * @var array|null
	 */
	private $userData = null;

	/**
	 * @var array|null
	 */
	private $groups = null;

	/**
	 * @param \BaseFacebook $facebook
	 */
	public function __construct(\BaseFacebook $facebook) {
		$this->facebook = $facebook;
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