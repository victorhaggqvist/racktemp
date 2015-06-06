<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 4:35 PM
 */

namespace AppBundle\Entity;


use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable{

    private $username;

    private $hashType;

    private $salt;

    private $shadowMeta;

    function __construct($username, $hashType, $salt, $shadowMeta) {
        $this->username = $username;
        $this->hashType = $hashType;
        $this->salt = $salt;
        $this->shadowMeta = $shadowMeta;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getHashType() {
        return $this->hashType;
    }

    /**
     * @return mixed
     */
    public function getSalt() {
        return $this->salt;
    }

    public function getPassword() {
        return null;
    }

    /**
     * @return mixed
     */
    public function getShadowMeta() {
        return $this->shadowMeta;
    }

    /**
     * @inheritdoc
     */
    public function getRoles() {
        return array('ROLE_USER');
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials() {
        return true;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize() {
        return serialize(array(
            $this->username,
            $this->hashType,
            $this->salt,
            $this->shadowMeta
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized) {
        list (
            $this->username,
            $this->hashType,
            $this->salt,
            $this->shadowMeta,
        ) = unserialize($serialized);
    }
}
