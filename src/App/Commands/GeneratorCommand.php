<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Generator extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:all')
            ->setDescription('Gera os arquivos de backend');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		return 1;
        // $name = $input->getArgument('name');
        // if ($name) {
        //     $text = 'Hello '.$name;
        // } else {
        //     $text = 'Hello';
        // }

        // if ($input->getOption('yell')) {
        //     $text = strtoupper($text);
        // }

        // $output->writeln($text);
    }
}
