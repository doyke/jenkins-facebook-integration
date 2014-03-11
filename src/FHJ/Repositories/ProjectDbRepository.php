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
	
	public function createProject(User $user, $name, $description) {
	    
	}
    
    public function updateProject(Project $project) {
        
    }
    
    public function deleteProject(Project $project) {
        
    }
    
    public function findAllProjects() {
        
    }
    
    public function findProjectsByUser(User $user) {
        
    }
    
    public function findProjectById($id) {
        
    }
	
} 