<?php

namespace FHJ\Listeners;

use Monolog\Logger;
use FHJ\Facebook\Api\EndpointFactory;
use FHJ\Facebook\Api\FacebookPostingHelper;
use FHJ\Facebook\FacebookConfig;
use FHJ\Repositories\UserDbRepositoryInterface;
use FHJ\Events\BuildStatusUpdateEvent;
use FHJ\Entities\User;
use FHJ\Entities\Project;

/**
 * SocialEventListener
 *
 * Posts status updates to Facebook.
 *
 * @package FHJ\Listeners
 */
class SocialEventListener {
    
    /**
     * @var Logger
     */ 
    private $logger;

	/**
	 * @var UserDbRepositoryInterface
	 */
	private $userRepository;

	private $fbConfig;
    
    public function __construct(UserDbRepositoryInterface $userRepository, FacebookConfig $fbConfig, Logger $logger) {
        $this->userRepository = $userRepository;
	    $this->fbConfig = $fbConfig;
	    $this->logger = $logger;
    }
    
    public function onProjectBuildStatusUpdate(BuildStatusUpdateEvent $event) {
        $this->logger->addInfo(sprintf('SocialEventListener: received BuildStatusUpdateEvent for project id "%d"',
	        $event->getProject()->getId()));

	    $user = $this->userRepository->findUserById($event->getProject()->getUserId());
	    if ($user === null) {
		    throw new \RuntimeException(sprintf('SocialEventListener: user not found for project id "%d"',
			    $event->getProject()->getId()));
	    }

	    $this->handleStatusUpdate($user, $event->getProject(), $event->getNewBuildState(), $event->getJobUrl());
	    $this->logger->addInfo('SocialEventListener: finished callback for BuildStatusUpdateEvent');
    }

	private function handleStatusUpdate(User $user, Project $project, $newBuildState, $jobUrl) {
		$endpointFactory = new EndpointFactory($this->fbConfig);
		$facebook = $endpointFactory->getFacebookApi($user);

		$message = sprintf('New build state of project "%s": %s', $project->getTitle(), $newBuildState);

		$postingHelper = new FacebookPostingHelper($facebook);
		$postingHelper->postMessageWithLinkToGroup($project->getFacebookGroupId(), $message, $jobUrl);
	}

}
