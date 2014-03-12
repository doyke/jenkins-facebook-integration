<?php

namespace FHJ\Repositories;

use Doctrine\DBAL\Connection;
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
	 *
	 * @throws \Exception
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId) {
        $this->getLogger()->addInfo('creating new user from facebook id',
            array('facebook_id' => $facebookUserId));
        
        $connection = $this->getConnection();
        $connection->beginTransaction();
        
        $user = null;
        try {
            $connection->insert($this->table, array(
                'facebook_id' => $connection->quote($facebookUserId, \PDO::PARAM_STR),
                'email' => '',
                'realname' => ''
            ), array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR
            ));

            $insertId = $connection->lastInsertId();
            $user = new User($insertId, $facebookUserId);
        
            $connection->commit();
        }  catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        
        return $user;
    }
    
    public function findUserByFacebookUserId($facebookUserId) {
	    $this->getLogger()->addInfo('looking up user by facebook id',
	        array('facebook_id' => $facebookUserId));

        $connection = $this->getConnection();
        $sql = sprintf('SELECT * FROM %s WHERE facebook_id = ?', $this->table);
        $statement = $connection->executeQuery($sql, array(
            $connection->quote($facebookUserId, \PDO::PARAM_STR)), array(\PDO::PARAM_STR));
            
        if ($statement->rowCount() === 0) {
            return null;
        } else if ($statement->rowCount() > 1) {
            throw new \RuntimeException(sprintf('more than one result found for facebook_id "%s"',
                $facebookUserId));
        }
            
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        
        return $this->fillUserEntity($result);
    }
    
    public function findUserById($id) {
        $this->getLogger()->addInfo('looking up user by id', array('id' => $id));

        $result = $this->fetchEntityById($this->table, $id);
        if ($result === null) {
            return null;
        }
        
        return $this->fillUserEntity($result);
    }
    
    public function updateUser(User $user) {
	    $this->getLogger()->addInfo('updating user', array('facebook_id' => $user->getUsername()));

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->update($this->table, array(
                'facebook_id' => $connection->quote($user->getFacebookUserId(), \PDO::PARAM_STR),
                'email' => $connection->quote($user->getEmail(), \PDO::PARAM_STR),
                'realname' => $connection->quote($user->getRealname(), \PDO::PARAM_STR),
                'is_login_allowed' => $user->isLoginAllowed(),
                'is_admin' => $user->isAdmin(),
            ), array(
                'id' => intval($user->getId())
            ), array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
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

        $this->deleteEntity($this->table, $user->getId());
    }
    
    /**
     * Fills a new User entity by using a result set. 
     *
     * @param array $resultSet The result set array
     * 
     * @return User
     */
    private function fillUserEntity(array $resultSet) {
        return new User($resultSet['id'], $resultSet['facebook_id'], $resultSet['email'],
            $resultSet['realname'],
	        // The values in the database are integers, the User class only accepts booleans
	        $resultSet['is_login_allowed'] ? true : false,
	        $resultSet['is_admin'] ? true : false
        );
    }

}
