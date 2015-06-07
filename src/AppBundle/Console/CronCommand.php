<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 8:37 PM
 */

namespace AppBundle\Console;


use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('racktemp:cron')
            ->setDescription('Cron runner');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('cronie');

        $sensorController = $this->getContainer()->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getSensors();

        $body = [];
        foreach ($sensors as $s) {
            $body[] = ['name' => $s->getName(),'temp' => $sensorController->readSensor($s)];
        }

        $client = new Client();

        $master = $this->getContainer()->getParameter('master_host');
        $apiKey = $this->getContainer()->getParameter('master_key');

        if (!preg_match('http', $master)) {
            $output->writeln("there is no protocol in 'master_host'");
            return;
        }

        $timestamp = time();
        $token = hash('sha512', $timestamp . $apiKey);

        $req = $client->createRequest('POST', sprintf('%s/api/record?token=%s&timestamp=%s', $master, $token, $timestamp), null, json_encode($body));
        $client->send($req);

    }


}
