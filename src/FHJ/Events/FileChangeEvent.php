<?php

namespace FHJ\Events;

use Symfony\Component\EventDispatcher\Event;
use FHJ\Entities\Project;

/**
 * FileChangeEvent
 * @package FHJ\Events
 */
final class FileChangeEvent extends Event {
    
    /**
     * @var Project
     */
    private $project;
    
    /**
     * @var array
     */
    private $changedFiles;
    
    public function __construct(Project $project, array $changedFiles) {
        $this->project = $project;
        $this->changedFiles = $changedFiles;
    }
    
    public function getProject() {
        return $this->project;
    }
    
    public function getChangedFiles() {
        return $this->changedFiles;
    }
    
}