<?php

namespace ElasticSearchClients\Command;

use ElasticSearchClients\Clients\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class RunCommand extends Command
{
    // @todo Dynamic list
    protected $clients = array(
        'sherlock' => 'ElasticSearchClients\Clients\SherlockPHP',
        'elastica' => 'ElasticSearchClients\Clients\Elastica',
        'nervetattoo' => 'ElasticSearchClients\Clients\Nervetattoo',
    );

    protected $methods = array(
        'getDocument',
        'searchDocument',
        'searchDocumentWithFacet',
        'searchOnDisconnectNode',
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
        $client_name = $input->getArgument('client');

        if (!isset($this->clients[$client_name])) {
            $output->writeln("Unknown client name.");
            return;
        }

        $stopwatch      = new Stopwatch();
        $stopwatch->start($client_name);

        for ($i = 0; $i < 1000; $i++)
        {
            /** @var $client ClientInterface */
            $client         = new $this->clients[$client_name];

            foreach ($this->methods as $method)
            {
                try
                {
                    $client->{$method}();
                }
                catch (\Exception $e)
                {
                    $output->writeln('Error: '.$e->getMessage());
                }
            }
        }


        $event = $stopwatch->stop($client_name);

        $output->write($client_name);
        $output->write("\t");
        $output->write($event->getDuration());
        $output->write("\t");
        $output->writeln($event->getMemory());
    }
}
