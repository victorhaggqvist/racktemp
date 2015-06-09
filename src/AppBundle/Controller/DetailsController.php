<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 3:12 AM
 */

namespace AppBundle\Controller;


use AppBundle\Util\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DetailsController extends Controller {

    /**
     * @Route("/details/{sensorname}", defaults={"sensorname"="_noname"}, name="details")
     */
    public function detailsAction(Request $request, $sensorname) {
        $sensorController = $this->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getSensors();

        if (count($sensors) < 1 && !$this->getParameter('dev-disable-redirects'))
            return $this->redirectToRoute('firsttime');

        $itemsPerPage = 50;
        $page = $request->query->get('page', 1);
        $pagesToDisplay = 20;

        if ($sensorname == "_noname") {
            $sensor = $sensors[0];
        } else {
            foreach ($sensors as $s) {
                if ($s->getName() == $sensorname)
                    $sensor = $s;
            }
        }

        $sensorTool = $this->get('app.sensor.sensor_tool');
        $total = $sensorTool->getListSize($sensor->getName());

        $paginator = new Paginator($itemsPerPage, $pagesToDisplay, $total);

        $list = $sensorTool->getList($sensor->getName(), (($page*$itemsPerPage)-$itemsPerPage), $itemsPerPage);

        $listStart = (($page*$itemsPerPage)-$itemsPerPage);
        $listEnd = ($page*$itemsPerPage>$total)?$total:($page*$itemsPerPage);

        $pagination = $paginator->getPagination($page, $sensor->getName());

        return $this->render(':default:detailed.html.twig',
            array(
                'sensors' => $sensors,
                'list' => $list,
                'listStart' => $listStart,
                'listEnd' => $listEnd,
                'sensor' => $sensor,
                'total' => $total,
                'pagination' => $pagination
            )
        );
    }

}
