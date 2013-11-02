<?php

namespace ElasticSearchClients\Command;

use ElasticSearchClients\Clients\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    // @todo Dynamic list
    protected $clients = array(
        'elastica' => 'ElasticSearchClients\Clients\Elastica'
    );

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the tests calls')
            ->addArgument(
                'client',
                InputArgument::REQUIRED,
                'The Client to test'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $input->getArgument('client');

        if (!isset($this->clients[$client])) {
            $output->writeln("Unknown client name.");
            return;
        }

        /** @var $client ClientInterface */
        $client = new $this->clients[$client];


        $client->getDocument();
        $client->searchDocument();
    }
}
