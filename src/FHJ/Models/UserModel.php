<?php

namespace FHJ\Models;

use FHJ\Entities\User;

/**
 * UserModel
 * @package FHJ\Model
 */
class UserModel extends User {

    /**
     * @var boolean
     */
    private $deletable;
    
    private $projectCount;
    
    public function __construct(User $user) {
        parent::__construct($user->getId(), $user->getFacebookUserId(), $user->getEmail(), $user->getFacebookAccessToken(),
            $user->getFacebookAccessExpiration(), $user->isLoginAllowed(), $user->isProjectCreationAllowed(),
            $user->isAdmin());
    }

    public function setDeletable($deletable) {
        $this->checkBoolean($deletable, 'deletable');
        $this->deletable = $deletable;
    }
    
    public function isDeletable() {
        return $this->deletable;
    }
    
    public function setProjectCount($projectCount) {
        $this->checkInt($projectCount, 'projectCount');
        $this->projectCount = $projectCount;
    }
    
    public function getProjectCount() {
        return $this->projectCount;
    }
}    