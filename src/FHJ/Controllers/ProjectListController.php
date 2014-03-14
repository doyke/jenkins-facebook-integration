<?php

namespace FHJ\Controllers;

/**
 * ProjectListController
 * @package FHJ\Controllers
 */
class ProjectListController extends BaseController {

    public function listOwnAction() {
        $user = $this->getSecurity()->getUser();
        $projects = $this->getProjectRepository()->findProjectsByUser($user);
        
        return $this->getTemplateEngine()->render('projects.html.twig', array(
            'projects'  => $projects
        ));
    }
    
    public function listAllAction() {
        return $this->getTemplateEngine()->render('projectsAll.html.twig', array(
            'projects'  => $this->getProjectRepository()->findAllProjects()
        ));
    }
    
}