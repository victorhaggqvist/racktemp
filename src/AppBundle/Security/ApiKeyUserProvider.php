<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 1:45 AM
 */

namespace AppBundle\Security;


use AppBundle\Entity\ApiKey;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface {

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Logger
     */
    private $logger;

    function __construct(EntityManager $em, Logger $logger) {

        $this->em = $em;
        $this->logger = $logger;
    }

    public function validateKeyPair($token, $timestamp) {
        $keys = $this->em->getRepository('AppBundle:ApiKey')->findAll();
        foreach ($keys as $key) {
            if (
                (
                    hash('sha512', $timestamp.$key->getKey()) == $token||
                    hash('sha512', $key->getKey().$timestamp) == $token
                )
                &&
                $this->checkTimestamp($timestamp)
            ){
                $key->setLastAccess(new \DateTime());
                $this->em->persist($key);
                $this->em->flush();
//                $this->updateKeyLastUsed($key['name']);
                return true;
            }
        }
        return false;
    }

    /**
     * Check if timestamp is in valid range
     * @param  int $timestamp Timestamp
     * @return boolean         If in valid range
     */
    private function checkTimestamp($timestamp) {
        $allowedDiff = 3600 * 12;
        $now = time();
        return abs($timestamp-$now) < $allowedDiff;
    }

    public function getWebKey() {
        $key = $this->em->getRepository('AppBundle:ApiKey')->findOneBy(array('name' => 'web'));
        if (!$key) {
            $key = ApiKey::create('web');
            $this->em->persist($key);
            $this->em->flush();
        }
        $keyPair = $this->makeKeyPair($key->getKey());

        return "'".$keyPair['timestamp']."','".$keyPair['token']."','".$_SERVER["HTTP_HOST"]."'";
    }

    public function getKey($key) {
        return $this->em->getRepository('AppBundle:ApiKey')->findOneBy(array('name' => $key));
    }

    /**
     * Generate a key pair from a key
     * @param  string $key Api key
     * @return array      keypair
     */
    private function makeKeyPair($key) {
        $ret['timestamp'] = time();
        $ret['token'] = hash('sha512', $ret['timestamp'].$key);
        return $ret;
    }

    public function getAllKeys() {
        return $keys = $this->em->getRepository('AppBundle:ApiKey')->findAll();
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username) {
        return new User('noname', null);
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user) {
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class) {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

    public function newKey($name) {
        $key = ApiKey::create($name);
        $this->em->persist($key);
        $this->em->flush();
    }

    public function deleteKey($keyId) {
        $key = $this->getKeyById($keyId);
        $this->em->remove($key);
        $this->em->flush();
    }

    private function getKeyById($keyId) {
        return $this->em->getRepository('AppBundle:ApiKey')->findOneBy(array('id' => $keyId));
    }

}
