<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateAppDefault extends Command
{
	protected function configure()
	{
		$this->setName('generate:default:app')
			->setDescription('Gera o arquivo padrão app.php na raiz');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateAppDefault();
		return 1;
	}
}
