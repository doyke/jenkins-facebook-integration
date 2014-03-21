<?php

namespace FHJ\Facebook;

use Doctrine\Common\Cache\Cache;
use Monolog\Logger;

/**
 * CachingFacebookDataRetriever
 * @package FHJ\Facebook
 */
class CachingFacebookDataRetriever extends FacebookDataRetriever {

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct(\BaseFacebook $facebook, Cache $cache, Logger $logger) {
		parent::__construct($facebook, $logger);

		$this->cache = $cache;
		$this->logger = $logger;
	}

	public function getEmail() {
		return $this->fetchData('email', function() {
			return parent::getEmail();
		});
	}

	public function getRealname() {
		return $this->fetchData('realname', function() {
			return parent::getRealname();
		});
	}

	public function getGroups() {
		return $this->fetchData('groups', function() {
			return parent::getGroups();
		});
	}

	/**
	 * Fetches the data from the cache or uses the supplied callable to get a fresh copy of the data.
	 *
	 * @param string $identifier The cache identifier
	 * @param callable $retrieveData An callable which returns the desired data
	 *
	 * @return mixed
	 */
	private function fetchData($identifier, callable $retrieveData) {
		$userIdentifier = $this->getUserId();
		$cacheIdentifier = $userIdentifier . '_' . $identifier;

		// If the $userIdentifier is null ALWAYS fetch the data. Otherwise we would save data with an invalid user id
		if ($userIdentifier !== null && $this->cache->contains($cacheIdentifier)) {
			$this->logger->addInfo(sprintf('fetch cached data for identifier "%s"', $cacheIdentifier));
			return $this->cache->fetch($cacheIdentifier);
		}

		$data = $retrieveData();
		$this->cache->save($cacheIdentifier, $data);
		$this->logger->addInfo(sprintf('saved data to cache for identifier "%s"', $cacheIdentifier));

		return $data;
	}

}