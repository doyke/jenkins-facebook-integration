<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ProjectListController
 * @package FHJ\Controllers
 */
class ProjectListController extends BaseController {

    public function listOwnAction(Request $request) {
        $user = $this->getSecurity()->getUser();
        $projects = $this->getProjectRepository()->findProjectsByUser($user);
        
        return $this->getTemplateEngine()->render('projects.html.twig', array(
            'projects'  => $projects
        ));
    }
    
    public function listAllAction(Request $request) {
        return $this->getTemplateEngine()->render('projectsAll.html.twig', array(
            'projects'  => $this->getProjectRepository()->findAllProjects()
        ));
    }
    
}