<?php

namespace ElasticSearchClients\Command;

use ElasticSearchClients\Clients\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class RunCommand extends Command
{
    // @todo Dynamic list would be nice
    protected $clients = array(
        'sherlock' => 'ElasticSearchClients\Clients\SherlockPHP',
        'elastica' => 'ElasticSearchClients\Clients\Elastica',
        'nervetattoo' => 'ElasticSearchClients\Clients\Nervetattoo',
        'elasticsearch' => 'ElasticSearchClients\Clients\Elasticsearch',
    );

    // @todo Dynamic list would be nice too!
    protected $methods = array(
        'getDocument',
        'searchDocument',
        'searchDocumentWithFacet',
        'searchOnDisconnectNode',
        'searchSuggestion',
        'indexRefresh',
        'indexStats',
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
            ->addOption('hide-errors', 's', InputOption::VALUE_OPTIONAL, "", false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client_name = $input->getArgument('client');
        $hide_errors = (bool) $input->getOption('hide-errors');

        if (!isset($this->clients[$client_name])) {
            $output->writeln("Unknown client name.");
            return;
        }

        $output->writeln("");
        $output->writeln("");
        $output->writeln($client_name." tests");
        $output->writeln("");

        $stopwatch      = new Stopwatch();
        $stopwatch->start($client_name);

        for ($i = 0; $i < 500; $i++)
        {
            /** @var $client ClientInterface */
            $client         = new $this->clients[$client_name];

            foreach ($this->methods as $method)
            {
                $stopwatch->start($method);

                try
                {
                    $client->{$method}();

                    $event = $stopwatch->stop($method);

                    if ($i === 499)
                    {
                        $this->writeEvent($event, $method, $output);
                    }
                }
                catch (\Exception $e)
                {
                    if (!$hide_errors)
                    {
                        $output->writeln('Error: '.$e->getMessage());
                    }

                    if ($i === 499)
                    {
                        $output->writeln($method."\t0\tFAIL");
                    }
                }
            }
        }


        $event = $stopwatch->stop($client_name);
        $this->writeEvent($event, $client_name, $output);
    }

    protected function writeEvent(StopwatchEvent $event, $name, $output)
    {
        $output->write($name);
        $output->write("\t");
        $output->write($event->getDuration());
        $output->write("\t");
        $output->writeln($event->getMemory());
    }
}
