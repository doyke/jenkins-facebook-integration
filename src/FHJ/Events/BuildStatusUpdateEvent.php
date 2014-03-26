<?php

namespace FHJ\Events;

use Symfony\Component\EventDispatcher\Event;
use FHJ\Entities\Project;

/**
 * BuildStatusUpdateEvent
 * @package FHJ\Events
 */
final class BuildStatusUpdateEvent extends Event {
    
    /**
     * @var Project
     */
    private $project;
    
    /**
     * @var string
     */
    private $newBuildState;

	/**
	 * @var string
	 */
	private $jobName;

	/**
	 * @var string
	 */
	private $jobUrl;
    
    public function __construct(Project $project, $newBuildState, $jobName, $jobUrl) {
        $this->project = $project;
        $this->newBuildState = $newBuildState;
	    $this->jobName = $jobName;
	    $this->jobUrl = $jobUrl;
    }
    
    public function getProject() {
        return $this->project;
    }
    
    public function getNewBuildState() {
        return $this->newBuildState;
    }

	public function getJobName() {
		return $this->jobName;
	}

	public function getJobUrl() {
		return $this->jobUrl;
	}

}