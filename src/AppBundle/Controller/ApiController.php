<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 3:55 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller {

    /**
     * @Route("/api/graph/span/{span}", name="api_graph_span")
     */
    public function indexAction(Request $request, $span) {
        if (!$this->verifySpan($span)) {
            return new Response(sprintf('Specified span <strong>%s</strong> is not valid.', $span), 400);
        }

        $graphApi = $this->get('app.api.graph_api');
        $graphData = $graphApi->getSpan($span);

        return new Response($graphData, 200, array('Content-Type' => 'application/json'));
    }

    private function verifySpan($span) {
        $valid = '(hour|day|week|month)';
        if (!preg_match($valid, $span)) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/api/record", name="api_record")
     * @param Request $request
     */
    public function recordAction(Request $request){
        $body = $request->getContent();
        $json = json_decode($body, true);

        $sensorController = $this->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getSensors();

        $sensorTool = $this->get('app.sensor.sensor_tool');

        foreach ($sensors as $s) {
            $sensorTool->addData($s->getName(), $json[$s->getUid()]);
        }

        return new Response('', 201);
    }
}
