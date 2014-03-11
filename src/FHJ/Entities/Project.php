<?php

namespace FHJ\Entities;

/**
 * Project
 * @package FHJ\Entities
 */
class Project {
    
    /**
     * @var int
     */ 
    private $id;
    
    /**
     * @var int
     */ 
    private $userId;
    
    /**
     * @var bool
     */ 
    private $enabled;
    
    /**
     * @var string
     */ 
    private $facebookGroupId;
    
    /**
     * @var string
     */ 
    private $secretKey;
    
    /**
     * @var string
     */ 
    private $svnplotDbPath;
    
    private $title;
    
    private $description;

    

}