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
    
    public function __construct(Project $project, $newBuildState) {
        $this->project = $project;
        $this->newBuildState = $newBuildState;
    }
    
    public function getProject() {
        return $this->project;
    }
    
    public function getNewBuildState() {
        return $this->newBuildState;
    }
    
}