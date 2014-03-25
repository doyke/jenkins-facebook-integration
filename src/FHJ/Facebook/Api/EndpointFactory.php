<?php

namespace FHJ\Facebook\Api;

use FHJ\Entities\User;

/**
 * EndpointFactory
 *
 * A factory to create a Facebook API endpoint for a given User.
 *
 * @package FHJ\Facebook\Api
 */
class EndpointFactory {

	/**
	 * Create a API endpoint for the given User object
	 *
	 * @param User $user The user
	 *
	 * @throws \LogicException
	 * @return \BaseFacebook
	 */
	public function getFacebookApi(User $user) {
		$facebookUserId = $user->getFacebookUserId();
		$accessToken = $user->getFacebookAccessToken();

		if (empty($facebookUserId)) {
			throw new \LogicException('Facebook user id is empty');
		}

		if (empty($accessToken)) {
			throw new \LogicException('Facebook access token is empty');
		}

		// TODO: Implement me
	}

}
