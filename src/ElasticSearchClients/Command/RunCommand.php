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
        ->addArgument('type',
            InputArgument::REQUIRED,
            'The type of benchmarking to perform.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        if ($type === 'transient') {
            $this->performTransientTests($input, $output);
        } elseif ($type === 'persistent') {
            $this->performPersistentTests($input, $output);
        }

    }

    private function performTransientTests(InputInterface $input, OutputInterface $output)
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

        $stopwatch = new Stopwatch();
        $total     = 0;

        for ($i = 0; $i < 500; $i++)
        {
            /** @var $client ClientInterface */
            $client         = new $this->clients[$client_name]('transient');

            foreach ($this->methods as $method)
            {
                try
                {
                    /** @var StopwatchEvent $event */
                    $event = $client->{$method}($stopwatch);

                    if ($i === 499)
                    {
                        $total += $event->getDuration();
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

        $this->writeTotal($total, $client_name, $output);
    }


    private function performPersistentTests(InputInterface $input, OutputInterface $output)
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

        $stopwatch = new Stopwatch();
        $total     = 0;

        /** @var $client ClientInterface */
        $client = new $this->clients[$client_name]('persistent');

        for ($i = 0; $i < 500; $i++)
        {
            foreach ($this->methods as $method)
            {
                try
                {
                    /** @var StopwatchEvent $event */
                    $event = $client->{$method}($stopwatch);

                    if ($i === 499)
                    {
                        $total += $event->getDuration();
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

        $this->writeTotal($total, $client_name, $output);
    }

    protected function writeEvent(StopwatchEvent $event, $name, $output)
    {
        $output->write(str_pad($name, 20, " ", STR_PAD_RIGHT));
        $output->write("\t");
        $output->write(str_pad($event->getDuration(), 10, " ", STR_PAD_RIGHT));
        $output->write("\t");
        $output->writeln(str_pad($event->getMemory(), 10, " ", STR_PAD_RIGHT));
    }

    protected function writeTotal($duration, $name, $output)
    {
        $output->write(str_pad($name, 20, " ", STR_PAD_RIGHT));
        $output->write("\t");
        $output->write(str_pad($duration, 10, " ", STR_PAD_RIGHT));
    }
}
