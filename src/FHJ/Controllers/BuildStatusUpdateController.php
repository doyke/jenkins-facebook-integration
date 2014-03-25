<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use FHJ\Entities\Project;
use FHJ\Events\EventIdentifiers;
use FHJ\Events\BuildStatusUpdateEvent;

/**
 * BuildStatusUpdateController
 * @package FHJ\Controllers
 */
class BuildStatusUpdateController extends BaseController {

    const ROUTE_UPDATE_STATUS = 'updateStatus';

    public function updateLastBuildStatusAction(Request $request, $secretKey) {
        $project = $this->getProjectRepository()->findProjectBySecretKey($secretKey);
	    $this->checkProjectPreconditions($secretKey, $project);

	    $jsonContent = $this->retrieveJsonObject($request);
        
        /* Example input:
        {"name":"JobName",
         "url":"JobUrl",
         "build":{"number":1,
        	  "phase":"STARTED",
        	  "status":"FAILED",
                  "url":"job/project/5",
                  "full_url":"http://ci.jenkins.org/job/project/5"
                  "parameters":{"branch":"master"}
        	 }
        }
        */

	    $this->checkForValidJson($jsonContent);

	    // We are only interested in the result if the build execution has been finished
        if ($jsonContent->build->phase !== 'FINISHED') {
            $this->getLogger()->addDebug(sprintf(
                'status update: throwing away status "%s" in phase "%s" for project id "%d"',
                $jsonContent->build->status, $jsonContent->build->phase, $project->getId()));
            
            return new Response(); // 200 OK
        }
        
        $currentStatus = strtoupper($jsonContent->build->status);
	    // Don't do anything if the state has not changed
        if ($project->getLastBuildState() === $currentStatus) {
            return new Response(); // 200 OK
        }

	    return $this->sendUpdateEvent($project, $currentStatus, $jsonContent);
    }

	/**
	 * @param $jsonContent
	 *
	 * @throws \Exception
	 */
	private function checkForValidJson($jsonContent) {
		if (!property_exists($jsonContent, 'name') || !property_exists($jsonContent, 'build')
				|| !property_exists($jsonContent->build, 'phase') || !property_exists($jsonContent->build, 'status')
				|| !property_exists($jsonContent->build, 'full_url')) {
			throw new \Exception('received incomple Jenkins status json');
		}
	}

	/**
	 * @param $secretKey
	 * @param $project
	 *
	 * @throws PreconditionFailedHttpException
	 * @throws NotFoundHttpException
	 */
	private function checkProjectPreconditions($secretKey, Project $project) {
		if ($project === null) {
			$this->getLogger()->addInfo(sprintf('no project found for secret key "%s"', $secretKey));

			throw new NotFoundHttpException('invalid secret key');
		}

		if (!$project->isEnabled()) {
			$this->getLogger()->addWarning(sprintf('project with id "%d" got update but is not enabled',
				$project->getId()));

			throw new PreconditionFailedHttpException('project is not enabled');
		}
}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private function retrieveJsonObject(Request $request) {
		$requestContent = $request->getContent();
		if ($requestContent === false) {
			throw new \Exception('content of request could not be read');
		}

		$jsonContent = json_decode($requestContent);
		if ($jsonContent === null) {
			throw new \Exception(sprintf('json decode error. last error: "%s"', json_last_error_msg()));
		}

		return $jsonContent;
	}

	/**
	 * @param Project $project
	 * @param string $currentStatus
	 * @param \stdClass $jsonContent
	 *
	 * @return Response
	 */
	private function sendUpdateEvent(Project $project, $currentStatus, $jsonContent) {
		try {
			// send an event
			$event = new BuildStatusUpdateEvent($project, $currentStatus, $jsonContent->name,
				$jsonContent->build->full_url);
			$modifiedEvent = $this->getEventDispatcher()->dispatch(EventIdentifiers::EVENT_BUILD_STATUS_UPDATE,
				$event);

			// get the project of the sent $modifiedEvent as some changes may have been made by the listeners
			$project = $modifiedEvent->getProject();
			$project->setLastBuildState($currentStatus);

			$this->getProjectRepository()->updateProject($project);
		} catch (\Exception $e) {
			$this->getLogger()->addDebug(sprintf(
				'status update: error at updating build status to "%s" for project id "%d"',
				$jsonContent->build->status, $project->getId()));

			return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return new Response(); // 200 OK
	}

}
