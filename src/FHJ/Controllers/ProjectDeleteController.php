<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FHJ\Entities\Project;

/**
 * ProjectDeleteController
 * @package FHJ\Controllers
 */
class ProjectDeleteController extends BaseController {

    const ROUTE_PROJECT_DELETE = 'projectDelete';

    public function deleteAction(Request $request, Project $project) {
        
    }
    
}