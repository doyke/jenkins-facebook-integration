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

    public function __construct() {
        
    }
    
    public function getId() {
        
    }
    
    public function setUserId($userId) {
        
    }
    
    public function getUserId() {
        
    }
    
    public function setEnabled($isEnabled) {
        $this->checkBoolean($isEnabled, 'isEnabled');
        $this->enabled = $isEnabled;
    }
    
    public function isEnabled() {
        return $this->enabled;
    }
    
    public function setFacebookGroupId($facebookGroupId) {
        if (empty($facebookGroupId)) {
            throw new InvalidArgumentException('The facebookGroupId cannot be empty');
        }
        
        $this->facebookUserId = $facebookGroupId;
    }
    
    public function getFacebookGroupId() {
        return $this->facebookGroupId;
    }
    
    public function setSecretKey($secretKey) {
        
    }
    
    public function getSecretKey() {
        
    }
    
    public function setSvnplotDbPath($svnplotDbPath) {
        
    }
    
    public function getSvnplotDbPath() {
        
    }
    
    public function setTitle($title) {
        
    }
    
    public function getTitle() {
        
    }
    
    public function setDescription($description) {
        
    }
    
    public function getDescription() {
        
    }
    
    private function checkBoolean($value, $fieldName) {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf('The field "%s" must be of type bool',
                $fieldName));
        }
    }

}