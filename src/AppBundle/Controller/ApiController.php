<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 3:55 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ApiController extends Controller {

    /**
     * @Route("/api/", name="api_home")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

}
