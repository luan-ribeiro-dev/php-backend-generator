<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateConfig extends Command
{
	protected function configure()
	{
		$this->setName('generate:config')
			->setDescription('Gera o arquivo de configuração em app/config.json');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Comandos::generateConfig();
		return 1;
	}
}
