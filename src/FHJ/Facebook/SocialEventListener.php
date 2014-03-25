<?php

namespace FHJ\Facebook;

use Monolog\Logger;
use FHJ\Facebook\Api\EndpointFactory;
use FHJ\Facebook\Api\FacebookPostingHelper;
use FHJ\Repositories\UserDbRepositoryInterface;
use FHJ\Events\BuildStatusUpdateEvent;
use FHJ\Entities\User;
use FHJ\Entities\Project;

/**
 * SocialEventListener
 * @package FHJ\Facebook
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
    
    public function __construct(UserDbRepositoryInterface $userRepository, Logger $logger) {
        $this->userRepository = $userRepository;
	    $this->logger = $logger;
    }
    
    public function onProjectBuildStatusUpdate(BuildStatusUpdateEvent $event) {
        $this->logger->addInfo(sprintf('SocialEventListener: received BuildStatusUpdateEvent for project id "%d"',
	        $event->getProject()->getId()));

	    $user = $this->userRepository->findUserById($event->getProject()->getUserId());
	    if ($user === null) {
		    throw new \RuntimeException(sprintf('user not found for project id "%d"', $event->getProject()->getId()));
	    }

	    $this->handleStatusUpdate($user, $event->getProject(), $event->getNewBuildState(), $event->getJobUrl());
	    $this->logger->addInfo('SocialEventListener: finished callback for BuildStatusUpdateEvent');
    }

	private function handleStatusUpdate(User $user, Project $project, $newBuildState, $jobUrl) {
		$endpointFactory = new EndpointFactory();
		$facebook = $endpointFactory->getFacebookApi($user);

		$message = sprintf('New build state: %s', $newBuildState);

		$postingHelper = new FacebookPostingHelper($facebook);
		$postingHelper->postMessageWithLinkToGroup($project->getFacebookGroupId(), $message, $jobUrl);
	}

}
