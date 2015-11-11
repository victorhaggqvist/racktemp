<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 9:57 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $settingkey;
    /**
     * @ORM\Column(type="string")
     */
    private $value;

    function __construct($key, $value) {
        $this->settingkey = $key;
        $this->value = $value;
    }


    /**
     * @return mixed
     */
    public function getKey() {
        return $this->settingkey;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }
}
