<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateDaoDefaultClasses extends Command
{
	protected function configure()
	{
		$this->setName('generate:default:dao')
			->setDescription('Gera o arquivo padrão de ConectaBanco e QueryBuilder em app/dao');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateDaoDefaultClasses();
		return 1;
	}
}
