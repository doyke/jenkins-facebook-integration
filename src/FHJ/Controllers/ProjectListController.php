<?php

namespace FHJ\Controllers;

use FHJ\Models\ProjectWithUserModel;

/**
 * ProjectListController
 * @package FHJ\Controllers
 */
class ProjectListController extends BaseController {

    const ROUTE_PROJECT_LIST_OWN = 'projects';
    
    const ROUTE_PROJECT_LIST_ALL = 'projectsAll';

    public function listOwnAction() {
        $user = $this->getSecurity()->getToken()->getUser();
        $projects = $this->getProjectRepository()->findProjectsByUser($user);
        
        return $this->getTemplateEngine()->render('projects.html.twig', array(
            'projects'  => $projects
        ));
    }
    
    public function listAllAction() {
        $rawProjects = $this->getProjectRepository()->findAllProjects();
        $users = $this->getUserRepository()->findAllUsers();
        
        $models = array();
        foreach ($rawProjects as $project) {
            $currentProjectUser = null;
            foreach ($users as $user) {
                if ($project->getUserId() === $user->getId()) {
                    $currentProjectUser = $user;
                    break;
                }
            }
            
            if ($currentProjectUser === null) {
                throw new \Exception(sprintf('no valid user found for user id "%d" of project "%d"',
                    $project->getUserId(), $project->getId()));
            }
            
            $models[] = new ProjectWithUserModel($project, $currentProjectUser);
        }
        
        return $this->getTemplateEngine()->render('projectsAll.html.twig', array(
            'projects'  => $models
        ));
    }
    
}