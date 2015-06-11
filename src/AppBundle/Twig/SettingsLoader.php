<?php
/**
 * User: Victor Häggqvist
 * Date: 6/11/15
 * Time: 11:02 AM
 */

namespace AppBundle\Twig;


use AppBundle\Util\Settings;
use Symfony\Bridge\Monolog\Logger;
use Twig_SimpleFunction;

class SettingsLoader extends \Twig_Extension {

    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var Logger
     */
    private $logger;

    function __construct(Settings $settings, Logger $logger) {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    public function getName() {
        return 'app_settingsloader';
    }

    public function getFunctions() {
        return array(
            new Twig_SimpleFunction('setting', array($this, 'setting'))
        );
    }

    public function setting($key) {
        $this->logger->debug(sprintf('get key %s', $key));
        return $this->settings->get($key);
    }

}
