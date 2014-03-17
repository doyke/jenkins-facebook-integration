<?php

namespace FHJ\Models;

use FHJ\Entities\User;
use FHJ\Entities\Project;

/**
 * ProjectWithUserModel
 * @package FHJ\Model
 */
class ProjectWithUserModel extends Project {

    /**
     * @var User
     */
    private $user;
    
    public function __construct(Project $project, User $user) {
        parent::__construct($project->getId(), $project->getUserId(), $project->getFacebookGroupId(),
            $project->isEnabled(), $project->getSecretKey(), $project->getSvnplotDbPath(),
            $project->getTitle(), $project->getDescription());
        
        $this->user = $user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }
    
    public function getUser() {
        return $this->user;
    }
}    