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
        $this->secretKey = $secretKey;
    }
    
    public function getSecretKey() {
        return $this->secretKey;
    }
    
    public function setSvnplotDbPath($svnplotDbPath) {
        $this->svnplotDbPath = $svnplotDbPath;
    }
    
    public function getSvnplotDbPath() {
        return $this->svnplotDbPath;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    private function checkBoolean($value, $fieldName) {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf('The field "%s" must be of type bool',
                $fieldName));
        }
    }

}