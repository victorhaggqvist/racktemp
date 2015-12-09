<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 4:13 PM
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Monolog\Logger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    /**
     * @var Logger
     */
    private $logger;

    function __construct(Logger $logger) {

        $this->logger = $logger;
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
        $process = new Process(sprintf("cat /etc/shadow | grep %s | awk -F : '{print $2}'", $username));

        try {
            $process->mustRun();

            if ($process->getOutput() == null) {
                $this->logger->error("cat /etc/shadow gave permission denied, make sure you have done 'sudo usermod -a -G shadow www-data'");
                $this->logger->error(sprintf('Failed to fetch user %s', $username));
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $username)
                );
            }
//            $this->logger->critical($process->getOutput());
            $shadow = explode("$", $process->getOutput());

            return new User($username, $shadow[1], $shadow[2], $shadow[3]);
        } catch (ProcessFailedException $e) {
            $this->logger->error($e->getMessage());
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
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
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class) {
        return $class === 'AppBundle\Entity\User';
    }
}
