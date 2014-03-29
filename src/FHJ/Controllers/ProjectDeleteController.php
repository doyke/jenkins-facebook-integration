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
        if ($request->getMethod() === 'POST') {
            return $this->doDelete($project);
        }

	    return $this->getTemplateEngine()->render('message.html.twig', array(
            'active' => 'projects',
            'message'  => sprintf('Do you really want to delete the project "%s"?',
                $project->getTitle()),
            'mode' => 'deleteCancel',
            'deletePath' => $this->generateRoute(self::ROUTE_PROJECT_DELETE, array('project' => $project->getId())),
            'cancelPath' => $this->generateRoute(ProjectListController::ROUTE_PROJECT_LIST_OWN)
        ));
    }
    
    private function doDelete(Project $project) {
        try {
            $this->getProjectRepository()->deleteProject($project);
            
            $this->getSession()->getFlashBag()->add('success', sprintf(
                'The project "%s" has been successfully deleted!', $project->getTitle()));
            return $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
        } catch (\Exception  $e) {
            $this->getLogger()->addError(sprintf('error when deleting project with id "%d"', $project->getId()),
                array('exception' => $e));
            
            $this->getSession()->getFlashBag()->add('error', sprintf( 'The deletion of the project "%s" failed!',
                $project->getTitle()));
            return $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
        }
    }
}