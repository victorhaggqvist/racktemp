<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 3:08 AM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontPageController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        $sensorController = $this->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getSensors();

        $settings = $this->get('app.util.settings');
        $settings->get('dev-ignore-no-sensors');

        $warnSensors = false;
        if (!$sensorController->checkSensors($sensors) &&
            !$settings->get('dev-ignore-no-sensors') &&
            count($sensors) > 0) {
            $warnSensors = true;
//            echo Alert::danger("Something is messed up with your sensors, you better check on them!");
        }


        $sensorTool = $this->get('app.sensor.sensor_tool');
        $activeSensors = array();
        if (count($sensors) > 0) {
            foreach ($sensors as $sensor) {
                if ($sensorTool->hasData($sensor->getName()))
                    $activeSensors[] = $sensor;
            }
        } else {
//            return $this->redirectToRoute('firsttime');
        }

        if (count($activeSensors) < 1) {
//            return $this->redirectToRoute('nodata');
        }


        $renderSensors = [];
        foreach($activeSensors as $sensor){
            $name = $sensor->getName();
            $currentTemp = $sensorTool->getTemp($name);
            $renderSensors[] = array('name' => $name, 'currentTemp' => $currentTemp);
        }


        $sensorStats = $this->get('app.sensor.sensor_stats');
        $stats = [];
        foreach($activeSensors as $sensor){
            $arr = [];
            $arr['name'] = $sensor->getName();

            $min = $sensorStats->getDailyStat($arr['name'], 'min');
            $arr['min'] = $min;
            if (!is_null($min)) {
                $arr['max'] = $sensorStats->getDailyStat($arr['name'], 'max');
                $arr['avg'] = $sensorStats->getDailyStat($arr['name'], 'avg');
            }else
                $arr['nostat'] = true;
            $stats[] = $arr;
        }



        $min = null;
        $max = null;
        $avg = 0;

        // calc weekly stats for all sensors that has been active

        $weeklyActiveSensors = 0;
        for ($i = 0; $i < count($activeSensors); $i++) {
            $name = $sensors[$i]->getName();

            $tempMin = $sensorStats->getWeeklyStat($name, 'min');

            if (!is_null($tempMin)) { // if there is any data for sensor
                $tempMax = $sensorStats->getWeeklyStat($name, 'max');
                if ($i == 0) {
                    $min = $tempMin['temp'];
                    $max = $tempMax['temp'];
                }else{
                    if($tempMin['temp'] < $min)
                        $min = $tempMin['temp'];
                    if($tempMax['temp'] < $max)
                        $max = $tempMax['temp'];
                }
                $avg += $sensorStats->getWeeklyStat($name, 'avg')['temp'];
                $weeklyActiveSensors++;
            }
        }



        $avg = ($avg == 0) ? 0 : $avg/$weeklyActiveSensors;
        if ($weeklyActiveSensors<1){
            $weekly = false;
        }else {
            $weekly = array('min' => $min, 'max' => $max, 'avg' => $avg);
        }

        $apiKeyProvider = $this->get('app.security.api_key_user_provider');
        $webKey = $apiKeyProvider->getWebKey();

        return $this->render('default/index.html.twig',
            array(
                'warnSensors' => $warnSensors,
                'renderSensors' => $renderSensors,
                'stats' => $stats,
                'weekly' => $weekly,
                'webKey' => $webKey
            )
        );
    }

}
