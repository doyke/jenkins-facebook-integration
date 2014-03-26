<?php

namespace FHJ\Facebook\Api;

use FHJ\Entities\User;
use FHJ\Facebook\FacebookConfig;

/**
 * EndpointFactory
 *
 * A factory to create a Facebook API endpoint for a given User.
 *
 * @package FHJ\Facebook\Api
 */
class EndpointFactory {

	/**
	 * @var FacebookConfig
	 */
	private $facebookConfig;

	public function __construct(FacebookConfig $facebookConfig) {
		$this->facebookConfig = $facebookConfig;
	}

	/**
	 * Create a API endpoint for the given User object
	 *
	 * @param User $user The user
	 *
	 * @throws \LogicException
	 * @return \BaseFacebook
	 */
	public function getFacebookApi(User $user) {
		$accessToken = $user->getFacebookAccessToken();

		if (empty($accessToken)) {
			throw new \LogicException('Facebook access token is empty');
		}

		$facebook = new SimpleFacebookEndpoint($this->generateFacebookConfigArray($this->facebookConfig));
		$facebook->setAccessToken($user->getFacebookAccessToken());

		return $facebook;
	}

	private function generateFacebookConfigArray(FacebookConfig $config) {
		return array(
			'appId'   => $config->getAppId(),
			'secret'  => $config->getSecret(),
			'appName' => 'https://apps.facebook.com/' . $config->getNamespace() . '/',
		);
	}

}
