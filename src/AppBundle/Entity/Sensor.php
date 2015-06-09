<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 9:48 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sensor")
 */
class Sensor {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $uid;

    /**
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    private $placement;

    public static function create($name, $uid) {
        $new = new self();
        $new->name = $name;
        $new->uid = $uid;
        return $new;
    }

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
    public function getUid() {
        return $this->uid;
    }


}
