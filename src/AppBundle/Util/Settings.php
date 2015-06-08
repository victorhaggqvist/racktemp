<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 4:07 PM
 */

namespace AppBundle\Util;


use AppBundle\Entity\Setting;
use Doctrine\ORM\EntityManager;

class Settings {


    /**
     * @var EntityManager
     */
    private $em;

    function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function get($key, $default = '') {
        $setting = $this->em->getRepository('AppBundle:Setting')->findOneBy(array('key' => $key));

        if (!$setting){
            return $default;
        }

        return $setting->getValue();
    }

    public function set($key, $value) {
        $setting = $this->em->getRepository('AppBundle:Setting')->findOneBy(array('key' => $key));

        if ($setting == null) {
            $setting = new Setting($key, $value);
        } else {
            $setting->setValue($value);
        }

        $this->em->persist($setting);
        $this->em->flush();
    }
//
//    public function getSet($keys) {
//        $this->em->getRepository('AppBundle:Setting')->createQueryBuilder()->
//    }

}
