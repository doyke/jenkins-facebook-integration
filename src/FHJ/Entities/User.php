<?php

namespace FHJ\Entities;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 * @package FHJ\Entities
 */
class User extends BaseEntity implements AdvancedUserInterface {
    
    /**
     * @var int
     */ 
    private $id;
    
    /**
     * @var string
     */ 
    private $facebookUserId;
    
    private $email;
    
    /**
     * @var string
     */ 
    private $facebookAccessToken;
    
    /**
     * @var \DateTime|null
     */ 
    private $facebookAccessExpiration;
    
    /**
     * @var bool
     */ 
    private $loginAllowed;
    
    /**
     * @var bool
     */ 
    private $admin;
    
    public function __construct($id, $facebookUserId, $email = '', $facebookAccessToken = '',
                                \DateTime $facebookAccessExpiration = null, $loginAllowed = false, $admin = false) {
        $this->setId($id);
        $this->setFacebookUserId($facebookUserId);
        $this->setEmail($email);
        $this->setFacebookAccessToken($facebookAccessToken);
        $this->setFacebookAccessExpiration($facebookAccessExpiration);
        $this->setLoginAllowed($loginAllowed);
        $this->setAdmin($admin);
    }
    
    private function setId($id) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('The id must be an integer');
        }
        
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setFacebookUserId($facebookUserId) {
        if (empty($facebookUserId)) {
            throw new \InvalidArgumentException('The facebookUserId cannot be empty');
        }
        
        $this->facebookUserId = $facebookUserId;
    }
    
    public function getFacebookUserId() {
        return $this->facebookUserId;
    }
    
    public function setEmail($email) {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Field "email" must contain a valid email or an empty string');
        }
        
        $this->email = $email;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setFacebookAccessToken($facebookAccessToken) {
        $this->facebookAccessToken = $facebookAccessToken;
    }
    
    public function getFacebookAccessToken() {
        return $this->facebookAccessToken;
    }

	/**
	 * @param $facebookAccessExpiration \DateTime|null
	 *
	 * @throws \InvalidArgumentException
	 */
    public function setFacebookAccessExpiration($facebookAccessExpiration) {
        if (!is_null($facebookAccessExpiration) && !$facebookAccessExpiration instanceof \DateTime) {
            throw new \InvalidArgumentException(
                'Field "facebookAccessExpiration" must either null or a valid DateTime instance');
        }
        
        $this->facebookAccessExpiration = $facebookAccessExpiration;
    }
    
    public function getFacebookAccessExpiration() {
        return $this->facebookAccessExpiration;
    }
    
    public function setLoginAllowed($isLoginAllowed) {
        $this->checkBoolean($isLoginAllowed, 'isLoginAllowed');
        $this->loginAllowed = $isLoginAllowed;
    }
    
    public function isLoginAllowed() {
        return $this->loginAllowed;
    }
    
    public function setAdmin($isAdmin) {
        $this->checkBoolean($isAdmin, 'isAdmin');
        $this->admin = $isAdmin;
    }
    
    public function isAdmin() {
        return $this->admin;
    }
    
    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles() {
        return $this->admin ? array('ROLE_ADMIN') : array('ROLE_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        return null;
    }

    /**
     * Returns the <b>facebook user id</b> used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->getFacebookUserId();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
        
    }
    
     /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired() {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return Boolean true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked() {
       return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired() {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return Boolean true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled() {
         return $this->loginAllowed;
    }
    
    public function equals($other) {
        if (!$other instanceof User) {
            return false;
        }
        
        return $this->getId() === $other->getId();
    }
    
}