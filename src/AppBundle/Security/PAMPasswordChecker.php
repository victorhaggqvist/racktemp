<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 5:26 PM
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Monolog\Logger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PAMPasswordChecker {


    /**
     * @var Logger
     */
    private $logger;

    function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Password checker inspired of http://www.raspberrypi.org/phpBB3/viewtopic.php?f=36&t=10992
     * Since the method to get PAM working invlovs adding www-data to shadow i figured i just stick
     * with this since php5-auth-pam is not inte Rasbian repos anyway
     * Appearently mkpasswd is not installed in Rasbian-2013-09-25 which this project is based on,
     * this is solved by installing whois
     *
     * This is a migration from the old implementation in RackTemp 1.0
     *
     * @param User $user
     * @param $password
     * @return bool
     */
    public function isPasswordValid(User $user, $password) {
        $hashId = $user->getHashType();
        $hashType = "";
        switch ($hashId) {
            case 1:
                $hashType = "MD5";
                break;
            case 5:
                $hashType = "SHA-256";
                break;
            case 6:
                $hashType = "SHA-512";
                break;
        }

        $process = new Process(sprintf("mkpasswd -m %s %s %s", $hashType, $password, $user->getSalt()));
        try {
            $process->mustRun();
            $passwordHashLine = $process->getOutput();

            $shadowLine = sprintf('$%s$%s$%s', $hashId, $user->getSalt(), $user->getShadowMeta());

            if ($shadowLine == $passwordHashLine){
                $this->logger->info(sprintf('Successful password check for %s', $user->getUsername()));
                return true;
            } else {
                $this->logger->debug('hash line mismatch');
                $this->logger->debug($passwordHashLine);
                $this->logger->debug($shadowLine);
            }
        } catch (ProcessFailedException $e) {
            $this->logger->error('The command "mkpasswd" is missing, if on ubuntu/debian install the "whois" package');
            $this->logger->error($e->getMessage());
        }

        return false;
    }
}
