<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateExampleModel extends Command
{
	protected function configure()
	{
		$this->setName('generate:example:model')
			->setDescription('Gera o arquivo de exemplo de modelo.json em app/generator/models/example.json');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateExampleModel();
		return 1;
	}
}
