<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateExceptionDefaultClasses extends Command
{
	protected function configure()
	{
		$this->setName('generate:default:exception')
			->setDescription('Gera o arquivo padrão de excecao em app/modelo');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateExceptionDefaultClasses();
		return 1;
	}
}
