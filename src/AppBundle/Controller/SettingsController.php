<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 2:37 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Sensor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller {

    /**
     * @Route("/settings")
     */
    public function indexAction() {
        return $this->redirectToRoute('settings_sensors');
    }

    /**
     * @Route("/settings/sensor", name="settings_sensors")
     */
    public function sensorsAction(Request $request) {
        $sensorController = $this->get('app.sensor.sensor_controller');
        $settings = $this->get('app.util.settings');

        $addStatus  = [];
        if ($request->request->has('add')) {
            $uids = $request->request->get('uid');
            $labels = $request->request->get('label');

            $currentSensors = $sensorController->getSensors();

            foreach ($uids as $key => $uid) {
                if (!empty($uid)) {               //if field not empty
                    if (!empty($labels[$key])) {  //if corresponding label is set

                        $exists = false;
                        foreach ($currentSensors as $cs)
                            if($cs->getUid() == $uid)     //if not all ready in
                                $exists = true;

                        if (!$exists){           //then we are good
                            $sensor = Sensor::create($labels[$key], $uid);
                            if ($sensorController->addSensor($sensor))
                                $addStatus[] = ['status' => 'success', 'msg' => sprintf('Sensor %s added', $uid)];
                            else
                                $addStatus[] = ['status' => 'danger', 'msg' => 'DB mess'];
                        }else
                            $addStatus[] = ['status' => 'danger', 'msg' => sprintf("It looks like %s already exists, so don't add it again", $uid)];
                    }else
                        $addStatus[] = ['status' => 'danger', 'msg' => sprintf("Please specify a Label for sensor %s", $uid)];
                }
            }
        }

        $attached = $sensorController->getAttachedSensors();
        $registered = $sensorController->getSensors();

        $new = null;
        if ($attached != null) {
            foreach ($attached as &$a){
                $exists=true;
                foreach ($registered as $r){
                    if ($r->getUid() == $a)
                        $exists = false;
                }
                if ($exists)
                    $new[] = $a;
            }
        }

        return $this->render(':settings:sensors.html.twig',
            array(
                'addStatus' => $addStatus,
                'manual_sensor_add' => $settings->get('manual-sensor-add'),
                'new' => $new,
                'registered' => $registered
            )
        );
    }

    /**
     * @Route("/settings/api", name="settings_api")
     */
    public function apiAction(Request $request) {
        $apiUserProvider = $this->get('app.security.api_key_user_provider');

        $keycreated = false;
        if ($request->request->has('submit-api')) {
            $name = $request->request->get('name');
            $apiUserProvider->newKey($name);
            $keycreated = $name;
        }

        $keydeleted = false;
        if ($request->query->has('delkey')) {
            $keyId =  $request->query->get('delkey');
            $apiUserProvider->deleteKey($keyId);
            $keydeleted = $keyId;
        }

        $keys = $apiUserProvider->getAllKeys();

        $key = $apiUserProvider->getKey('web');
        $sample = [];
        $sample['timestamp'] = time();
        $sample['token'] = hash('sha512', $sample['timestamp'] . $key->getKey());

        return $this->render(':settings:api.html.twig',
            array(
                'keys' => $keys,
                'samplekey' => $sample,
                'keycreated' => $keycreated,
                'keydeleted' => $keydeleted
            )
        );
    }

    /**
     * @Route("/settings/logging", name="settings_logging")
     */
    public function loggingAction() {

    }

    /**
     * @Route("/settings/general", name="settings_general")
     */
    public function generalAction(Request $request) {
        $settings = $this->get('app.util.settings');

        if ($request->request->has('submit')) {
//            $auth = $request->request->has('auth');
//            $useCdn = $request->request->has('use-cdn');
            $manualSensorAdd = $request->request->has('manual-sensor-add');
//            $sendStats = $request->request->has('send-stats');

//            $settings->set('send-stats', $sendStats);
            $settings->set('manual-sensor-add', $manualSensorAdd);
        }

        return $this->render(':settings:general.html.twig');
    }

    /**
     * @Route("/settings/notifications", name="settings_notifications")
     */
    public function notificationsAction(Request $request) {
        $sensorController = $this->get('app.sensor.sensor_controller');
        $settings = $this->get('app.util.settings');
        $sensors = $sensorController->getSensors();

        $settingsupdate = false;
        if ($request->request->has('submit-notification')) {
            $mgKey = $request->request->get('mg-key');
            $mgDomain = $request->request->get('mg-domain');
            $mgTo = $request->request->get('mg-to');

            // filter recipients field
            $to = explode(',', $mgTo);
            $to = array_map('trim', $to);
            $to = array_filter($to);
            $to = count($to)>1?implode(',', $to):$to[0];

            $settings->set('mg-domain', $mgDomain);
            $settings->set('mg-key', $mgKey);
            $settings->set('mg-to', $mgTo);

            foreach ($sensors as $sensor) {
                $settings->set('tempt-'.$sensor->getName().'-max', $request->request->get(sprintf('tempt-%s-max', $sensor->getName())));
                $settings->set('tempt-'.$sensor->getName().'-min', $request->request->get(sprintf('tempt-%s-min', $sensor->getName())));
            }

            $settings->set('notifications-enabled', ($request->request->get('notifications-enabled')=="on")?1:0);
            $settings->set('notifications-interval', $request->request->get('notifications-interval'));

            $settingsupdate = true;
        }

        $testresult = false;
        if ($request->request->has('send-test')) {
            $mailer = $this->get('app.util.mailer');
            $test = $mailer->sendTest();

            if (strpos($test, 'Mailer Error') !== false) {
                $testresult = ['status' => 'warning', 'msg' => sprintf('Message Error! %s', $test)];
            }else{
                $testresult = ['status' => 'success', 'msg' => sprintf('Sweet! %s', $test)];
            }
        }

        return $this->render(':settings:notifications.html.twig',
            array(
                'settingsupdate' => $settingsupdate,
                'testresult' => $testresult,
                'sensors' => $sensors
            )
        );
    }
}
