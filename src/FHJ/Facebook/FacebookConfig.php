<?php


namespace FHJ\Facebook;

/**
 * FacebookConfig
 * @package FHJ\Facebook
 */
class FacebookConfig {

	private $appId;

	private $secret;

	private $namespace;

	public function __construct($appId, $namespace, $secret) {
		$this->appId = $appId;
		$this->namespace = $namespace;
		$this->secret = $secret;
	}


	/**
	 * @param mixed $appId
	 */
	public function setAppId($appId) {
		$this->appId = $appId;
	}

	/**
	 * @return mixed
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * @param mixed $namespace
	 */
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	/**
	 * @return mixed
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * @param mixed $secret
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
	}

	/**
	 * @return mixed
	 */
	public function getSecret() {
		return $this->secret;
	}

} 