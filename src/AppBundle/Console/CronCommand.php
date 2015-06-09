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
        $logger = $this->getContainer()->get('logger');

        $sensorController = $this->getContainer()->get('app.sensor.sensor_controller');
        $sensors = $sensorController->getAttachedSensors();

        $body = [];
        foreach ($sensors as $s) {
            $body[$s] = $sensorController->readSensorByUid($s);
            $logger->info(sprintf('cron for %s', $s));
        }


        $master = $this->getContainer()->getParameter('master_host');
        $apiKey = $this->getContainer()->getParameter('master_key');

        if (!preg_match('/http/', $master)) {
            $output->writeln("there is no protocol in 'master_host'");
            $logger->warning("there is no protocol in 'master_host', aborting");
            return;
        }

        $timestamp = time();
        $token = hash('sha512', $timestamp . $apiKey);
        $jsonbody = json_encode($body);
        $output->writeln($jsonbody);

        $client = new Client();
        $client->setDefaultOption('verify', false);
        $req = $client->createRequest('POST', sprintf('%s/api/record?token=%s&timestamp=%s', $master, $token, $timestamp), null, $jsonbody);
//        $req->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
//        $req->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);
        $resp = $client->send($req);

        if ($resp->getStatusCode() == 201) {
            $output->writeln('data sent');
            $logger->info('data sent');
        } else {
            $logger->warning(sprintf('slave -> master failed: (%s) %s', $resp->getStatusCode(), $resp->getBody()));
        }
    }


}
