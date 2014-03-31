<?php

namespace FHJ\Repositories;

use FHJ\Entities\User;
use FHJ\Entities\Project;

/**
 * ProjectDbRepository
 * @package FHJ\Repositories
 */
class ProjectDbRepository extends BaseRepository implements ProjectDbRepositoryInterface {
	
	private $table = 'projects';
	
	public function createProject(User $user, $title, $description, $facebookGroupId, $enabled) {
	    $this->getLogger()->addInfo('creating new project from user id', array('user_id' => $user->getId()));
	    $this->checkInt($user->getId(), 'user#id');
		$this->checkBool($enabled, 'enabled');
	    $this->checkNotEmpty($title, 'title');
	    $this->checkNotEmpty($facebookGroupId, 'facebookGroupId');
        
        $secretKey = md5(uniqid($title)) . sha1(time());
        $secretKey = strtolower(substr($secretKey, 0, 60));
        
        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $connection->insert($this->table, array(
                'user_id' => intval($user->getId()),
                'is_enabled' => $enabled,
                'facebook_group_id' => $facebookGroupId,
                'title' => $title,
                'description' => $description,
                'secret_key' => $secretKey
            ), array(
                \PDO::PARAM_INT,
                'boolean',
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR
            ));

            $insertId = $connection->lastInsertId();
            $project = new Project(intval($insertId), $user->getId(), $facebookGroupId);
        
            $connection->commit();
        }  catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        
        return $project;
	}
    
    public function updateProject(Project $project) {
        $this->getLogger()->addInfo('updating project', array('id' => $project->getId()));

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->update($this->table, array(
                'user_id' => intval($project->getUserId()),
                'is_enabled' => $project->isEnabled(),
                'facebook_group_id' => $project->getFacebookGroupId(),
                'secret_key' => $project->getSecretKey(),
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'last_build_state' => $project->getLastBuildState(),
            ), array(
                'id' => intval($project->getId())
            ), array(
                \PDO::PARAM_INT,
                'boolean',
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT
            ));
        
            $connection->commit();
        }  catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }
    
    public function deleteProject(Project $project) {
        $this->getLogger()->addInfo('deleting project', array('id' => $project->getId()));

        $this->deleteEntity($this->table, $project->getId());
    }
    
    public function findAllProjects() {
        $this->getLogger()->addInfo('looking up all projects');

        $sql = sprintf('SELECT * FROM %s', $this->table);
        $statement = $this->getConnection()->executeQuery($sql);

	    $rawResults = $this->fetchManyEntitiesBySql($statement);
	    $results = array();
	    foreach ($rawResults as $rawResult) {
		    $results[] = $this->fillProjectEntity($rawResult);
	    }

	    return $results;
    }
    
    public function findProjectsByUser(User $user) {
        $this->getLogger()->addInfo('looking up projects by user id', array('user_id' => $user->getId()));

        $sql = sprintf('SELECT * FROM %s WHERE user_id = ?', $this->table);
        $statement = $this->getConnection()->executeQuery($sql, array(intval($user->getId())),
            array(\PDO::PARAM_INT));

	    $rawResults = $this->fetchManyEntitiesBySql($statement);
	    $results = array();
	    foreach ($rawResults as $rawResult) {
		    $results[] = $this->fillProjectEntity($rawResult);
	    }

	    return $results;
    }

	/**
	 * {@inheritdoc}
	 */
	public function findProjectById($id) {
        $this->getLogger()->addInfo('looking up project by id', array('id' => $id));
        $this->checkInt($id, 'id');

        $result = $this->fetchEntityById($this->table, $id);
        if ($result === null) {
            return null;
        }
        
        return $this->fillProjectEntity($result);
    }

	/**
	 * {@inheritdoc}
	 */
	public function findProjectBySecretKey($secretKey) {
        $this->getLogger()->addInfo('looking up project by secret key', array('secret_key' => $secretKey));

        $sql = sprintf('SELECT * FROM %s WHERE secret_key = ?', $this->table);
        $statement = $this->getConnection()->executeQuery($sql, array($secretKey), array(\PDO::PARAM_STR));

        $result = $this->fetchEntityBySql($statement);
        if ($result === null) {
            return null;
        }
        
        return $this->fillProjectEntity($result);
    }
	
	public function findProjectCountByUser(User $user) {
	    $this->getLogger()->addInfo('looking up project count by user id', array('user_id' => $user->getId()));

        $sql = sprintf('SELECT COUNT(Id) FROM %s WHERE user_id = ?', $this->table);
        $statement = $this->getConnection()->executeQuery($sql, array(intval($user->getId())),
            array(\PDO::PARAM_INT));

        return intval($statement->fetchColumn(0));
	}
	
	/**
     * Fills a new Project entity by using a result set. 
     *
     * @param array $resultSet The result set array
     * 
     * @return Project
     */
    private function fillProjectEntity(array $resultSet) {

        return new Project(intval($resultSet['id']), intval($resultSet['user_id']),
            $resultSet['facebook_group_id'],
            // The values in the database are integers, the User class only accepts booleans
	        $resultSet['is_enabled'] ? true : false,
	        $resultSet['secret_key'], $resultSet['svnplot_db_path'], $resultSet['title'],
	        $resultSet['description'], $resultSet['last_build_state']
        );
    }
	
} 