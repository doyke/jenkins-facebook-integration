<?php

namespace FHJ\Facebook;

use Monolog\Logger;
use FHJ\Events\BuildStatusUpdateEvent;

/**
 * SocialEventListener
 * @package FHJ\Facebook
 */
class SocialEventListener {
    
    /**
     * @var Logger
     */ 
    private $logger;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }
    
    public function onProjectBuildStatusUpdate(BuildStatusUpdateEvent $event) {
        
    }
    
}
