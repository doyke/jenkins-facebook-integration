<?php

namespace FHJ\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
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
	
	protected function fetchEntityById($table, $idValue) {
	    $sql = sprintf('SELECT * FROM %s WHERE id = ?', $table);
        $statement = $this->connection->executeQuery($sql, array(intval($idValue)), array(\PDO::PARAM_INT));

        return $this->fetchEntityBySql($statement);
	}
	
	protected function fetchEntityBySql(Statement $statement) {
        if ($statement->rowCount() === 0) {
            return null;
        } else if ($statement->rowCount() > 1) {
            throw new \RuntimeException('more than one result found for query');
        }
            
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        
        return $result;
	}
	
	protected function fetchManyEntitiesBySql(Statement $statement) {
	    $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        
        return $results;
	}
	
	protected function deleteEntity($table, $idValue) {
	    $this->connection->transactional(function(Connection $connection) use ($table, $idValue) {
	        $connection->delete($table, array(
		        'id' => intval($idValue)
	        ), array(
		        \PDO::PARAM_INT
	        ));
	    });
	}
	
	protected function checkInt($value, $fieldName) {
	    if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be of type int', $fieldName));
        }
	}
	
	protected function checkNotEmpty($value, $fieldName) {
	    if (empty($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must not be empty', $fieldName));
        }
	}
}
