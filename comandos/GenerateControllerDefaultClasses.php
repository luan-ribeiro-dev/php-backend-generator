<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateControllerDefaultClasses extends Command
{
	protected function configure()
	{
		$this->setName('generate:default:controle')
			->setDescription('Gera o arquivo padrão de controle: Controle.php e Constantes.php em app/controle');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateControllerDefaultClasses();
		return 1;
	}
}
