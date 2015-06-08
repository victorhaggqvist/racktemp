<?php
/**
 * User: Victor Häggqvist
 * Date: 6/8/15
 * Time: 10:45 PM
 */

namespace AppBundle\Console;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadSensorsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('racktemp:readsensors')
            ->setDescription('Read temp from sensors');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $sensorController = $this->getContainer()->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getAttachedSensors();

        foreach ($sensors as $s) {
            $output->writeln(sprintf('%s: %s', $s, $sensorController->readSensorByUid($s)));
        }
    }

}
