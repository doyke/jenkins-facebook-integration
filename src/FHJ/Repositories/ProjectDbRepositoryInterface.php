<?php

namespace FHJ\Repositories;

use FHJ\Entities\User;
use FHJ\Entities\Project;

interface ProjectDbRepositoryInterface {

    public function createProject(User $user, $name, $description, $facebookGroupId, $enabled);
    
    public function updateProject(Project $project);
    
    public function deleteProject(Project $project);
    
    public function findAllProjects();
    
    public function findProjectsByUser(User $user);

	/**
	 * @param $id
	 *
	 * @return Project|null
	 */
    public function findProjectById($id);

	/**
	 * @param $secretKey
	 *
	 * @return Project|null
	 */
	public function findProjectBySecretKey($secretKey);
    
    public function findProjectCountByUser(User $user);

} 