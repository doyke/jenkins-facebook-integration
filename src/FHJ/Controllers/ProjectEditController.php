<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FHJ\Entities\Project;

/**
 * ProjectEditController
 * @package FHJ\Controllers
 */
class ProjectEditController extends BaseController {

    const ROUTE_PROJECT_EDIT = 'projectEdit';
    
    const ROUTE_PROJECT_NEW = 'projectNew';

    public function editAction(Request $request, Project $project) {
        
    }
    
    public function newAction(Request $request) {
        
    }
    
}