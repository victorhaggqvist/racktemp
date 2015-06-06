<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 4:07 PM
 */

namespace AppBundle\Util;


use Doctrine\ORM\EntityManager;

class Settings {


    /**
     * @var EntityManager
     */
    private $em;

    function __construct(EntityManager $em) {
        $this->em = $em;
    }
}
