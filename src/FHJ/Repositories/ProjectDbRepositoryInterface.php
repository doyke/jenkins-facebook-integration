<?php

namespace FHJ\Repositories;

use FHJ\Entities\User;
use FHJ\Entities\Project;

interface ProjectDbRepositoryInterface {

    public function createProject(User $user, $name, $description);
    
    public function updateProject(Project $project);
    
    public function deleteProject(Project $project);
    
    public function findAllProjects();
    
    public function findProjectsByUser(User $user);
    
    public function findProjectById($id);

} 