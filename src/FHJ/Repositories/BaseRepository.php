<?php

namespace FHJ\Repositories;

use Doctrine\DBAL\Connection;
use Monolog\Logger;

class BaseRepository {

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct(Connection $connection, Logger $logger) {
		$this->connection = $connection;
		$this->logger = $logger;
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	protected function getConnection() {
		return $this->connection;
	}

	/**
	 * @return \Monolog\Logger
	 */
	protected function getLogger() {
		return $this->logger;
	}

}
