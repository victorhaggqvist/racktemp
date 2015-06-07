<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 8:52 PM
 */

namespace AppBundle\Console;


use AppBundle\Sensor\SensorController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadTempCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('racktemp:readtemp')
            ->setDescription('Read temp from sensors');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $sensorController = $this->getContainer()->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getSensors();

        foreach ($sensors as $s) {
            $output->writeln(sprintf('%s: %s', $s->getName(), $sensorController->readSensor($s)));
        }

    }


}
