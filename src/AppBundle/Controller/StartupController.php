<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 12:48 AM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StartupController extends Controller {

    /**
     * @Route("/firsttime", name="firsttime")
     */
    public function firsttimeAction() {
        return new Response('firsttime');
    }

    /**
     * @Route("/nodata", name="nodata")
     */
    public function nodataAction() {
        return new Response('nodata');
    }

}
