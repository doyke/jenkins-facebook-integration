<?php

namespace FHJ\Facebook\Api;

/**
 * SimpleFacebook
 *
 * A Facebook API class that does not use any sessions.
 *
 * @package FHJ\Facebook\Api
 */
class SimpleFacebookEndpoint extends \BaseFacebook {

	/**
	 * Stores the given ($key, $value) pair, so that future calls to
	 * getPersistentData($key) return $value. This call may be in another request.
	 *
	 * @param string $key
	 * @param array $value
	 *
	 * @return void
	 */
	protected function setPersistentData($key, $value) {

	}

	/**
	 * Get the data for $key, persisted by BaseFacebook::setPersistentData()
	 *
	 * @param string $key The key of the data to retrieve
	 * @param boolean $default The default value to return if $key is not found
	 *
	 * @return mixed
	 */
	protected function getPersistentData($key, $default = false) {
		return $default;
	}

	/**
	 * Clear the data with $key from the persistent storage
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	protected function clearPersistentData($key) {

	}

	/**
	 * Clear all data from the persistent storage
	 * @return void
	 */
	protected function clearAllPersistentData() {

	}
}
