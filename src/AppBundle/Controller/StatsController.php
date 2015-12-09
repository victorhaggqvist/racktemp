<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 7:28 PM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatsController extends Controller {

    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction() {
        $apiKeyProvider = $this->get('app.security.api_key_user_provider');
        $webKey = $apiKeyProvider->getWebKey();

        return $this->render(':stats:index.html.twig',
            array(
                'webKey' => $webKey
            )
        );
    }

}
