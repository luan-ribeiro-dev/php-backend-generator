<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
 
class GenerateAllBackend extends Command
{
    protected function configure()
    {
        $this->setName('generate:all')
            ->setDescription('Gera todos os arquivos do backend do sistema')
            ->setHelp('Demonstration of custom commands created by Symfony Console component.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$output->writeln("OBA");
		return 1;
    }
}