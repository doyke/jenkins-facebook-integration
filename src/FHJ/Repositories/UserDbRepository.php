<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;
use FHJ\Entities\User;

/**
 * UserDbRepository
 * @package FHJ\Repositories
 */
class UserDbRepository extends BaseRepository implements UserDbRepositoryInterface {

    private $table = 'users';

	/**
	 * @param $facebookUserId string Facebook user id
	 * @param $email string Email address of user
	 * @param $accessToken string The facebook access token
	 *
	 * @throws \Exception
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId, $email, $accessToken) {
        $this->getLogger()->addInfo('creating new user from facebook id',
            array('facebook_id' => $facebookUserId));
        $this->checkNotEmpty($facebookUserId, 'facebookUserId');
        
        $connection = $this->getConnection();
        $connection->beginTransaction();
        
        $user = null;
	    $loginAllowed = true;
        try {
            $connection->insert($this->table, array(
                'facebook_id' => $facebookUserId,
                'email' => $email,
	            'facebook_access_token' => $accessToken,
	            'is_login_allowed' => $loginAllowed
            ), array(
                \PDO::PARMA_STR,
                \PDO::PARAM_STR,
	            'boolean'
            ));

            $insertId = $connection->lastInsertId();
            $user = new User(intval($insertId), $facebookUserId, $email, $accessToken);
	        $user->setLoginAllowed($loginAllowed);
        
            $connection->commit();
        }  catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        
        return $user;
    }
    
    public function updateUser(User $user) {
	    $this->getLogger()->addInfo('updating user', array('facebook_id' => $user->getUsername()));

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->update($this->table, array(
                'facebook_id' => $user->getFacebookUserId(),
                'email' => $user->getEmail(),
                'facebook_access_token' => $user->getFacebookAccessToken(),
                'facebook_access_expiration' => $user->getFacebookAccessExpiration(),
                'is_login_allowed' => $user->isLoginAllowed(),
	            'is_project_creation_allowed' => $user->isProjectCreationAllowed(),
                'is_admin' => $user->isAdmin(),
            ), array(
                'id' => intval($user->getId())
            ), array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                'datetime',
                'boolean',
                'boolean',
	            'boolean',
                \PDO::PARAM_INT
            ));
        
            $connection->commit();
        }  catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }
    
    public function deleteUser(User $user) {
        $this->getLogger()->addInfo('deleting user', array('id' => $user->getId()));

        $that = $this;
        $this->getConnection->transactional(function() use ($that, $user) {
            $projectRepo = $that->getProjectRepository();
            $projects = $projectRepo->findProjectsByUser($user);
            
            foreach ($projects as $project) {
                $projectRepo->deleteProject($project);
            }
            
            $that->deleteEntity($that->table, $user->getId());
        });
    }
    
    public function findAllUsers() {
        $this->getLogger()->addInfo('looking up all users');
        
        $sql = sprintf('SELECT * FROM %s', $this->table);
        $statement = $this->getConnection()->executeQuery($sql);
        
        return $this->fetchManyEntitiesBySql($statement);
    }
    
    public function findUserByFacebookUserId($facebookUserId) {
	    $this->getLogger()->addInfo('looking up user by facebook id',
	        array('facebook_id' => $facebookUserId));

        $connection = $this->getConnection();
        $sql = sprintf('SELECT * FROM %s WHERE facebook_id = ?', $this->table);
        $statement = $connection->executeQuery($sql, array($facebookUserId), array(\PDO::PARAM_STR));
            
        $result = $this->fetchEntityBySql($statement);
        if ($result === null) {
            return null;
        }
        
        return $this->fillUserEntity($result);
    }
    
    public function findUserById($id) {
        $this->checkInt($id, 'id');
        $this->getLogger()->addInfo('looking up user by id', array('id' => $id));

        $result = $this->fetchEntityById($this->table, $id);
        if ($result === null) {
            return null;
        }
        
        return $this->fillUserEntity($result);
    }
    
    /**
     * Fills a new User entity by using a result set. 
     *
     * @param array $resultSet The result set array
     * 
     * @return User
     */
    private function fillUserEntity(array $resultSet) {
        $expirationDate = null;
        if ($resultSet['facebook_access_expiration'] !== null
                && intval($resultSet['facebook_access_expiration']) > 0) {
            $expirationDate = \DateTime::createFromFormat('U', $resultSet['facebook_access_expiration']);
        }
        
        return new User(intval($resultSet['id']), $resultSet['facebook_id'], $resultSet['email'],
            $resultSet['facebook_access_token'], $expirationDate,
	        // The values in the database are integers, the User class only accepts booleans
	        $resultSet['is_login_allowed'] ? true : false,
	        $resultSet['is_project_creation_allowed'] ? true : false,
	        $resultSet['is_admin'] ? true : false
        );
    }

}
