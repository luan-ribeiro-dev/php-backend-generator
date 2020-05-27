<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateSQL extends Command
{
	protected function configure()
	{
		$this->setName('generate:sql')
			->setDescription('Um arquivo sql de acordo com o modelo');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateSQL();
		return 1;
	}
}
