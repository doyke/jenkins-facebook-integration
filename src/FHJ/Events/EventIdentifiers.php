<?php

namespace FHJ\Events;

/**
 * EventIdentifiers
 * @package FHJ\Events
 */
final class EventIdentifiers {
    
    /**
     * The project.buildStatusUpdate event is thrown each time he status of an
     * Jenkins build has changed
     *
     * The event listener receives an FHJ\Events\BuildStatusUpdateEvent instance.
     *
     * @var string
     */
    const EVENT_BUILD_STATUS_UPDATE = 'project.buildStatusUpdate';
    
    /**
     * The project.fileChange event is thrown each time a set of files has been
     * changed in a project
     *
     * The event listener receives an FHJ\Events\FileChangeEvent instance.
     *
     * @var string
     */
    const EVENT_FILE_CHANGE = 'project.fileChange';
    
}