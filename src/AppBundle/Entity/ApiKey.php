<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 9:53 PM
 */

namespace AppBundle\Entity;


/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="api_key")
 */
class ApiKey {

    /**
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $name;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $apikey;
    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    private $lastAccess;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getKey() {
        return $this->apikey;
    }

    /**
     * @return mixed
     */
    public function getLastAccess() {
        return $this->lastAccess;
    }

    /**
     * @param mixed $lastAccess
     */
    public function setLastAccess($lastAccess) {
        $this->lastAccess = $lastAccess;
    }


    public static function create($name) {
        $src = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $key = '';

        for ($i = 0; $i < 50; $i++){
            $key .=substr($src,mt_rand(0,strlen($src)),1);
        }

        $new = new self();
        $new->apikey = $key;
        $new->name = $name;
        return $new;
    }
}
