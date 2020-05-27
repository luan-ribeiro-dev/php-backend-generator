<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateModels extends Command
{
	protected function configure()
	{
		$this->setName('generate:models')
			->setDescription('Gera todos os modelos de objetos que estão em app/generator/modelos')
			->addArgument('override', InputArgument::OPTIONAL, "Sobreescreve os arquivos do modelo em app/modelo", "n");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$reponse = false;
		if ($input->getArgument('override') === "s" || $input->getArgument('override') === "1")
			$reponse = true;
		Comandos::generateModels($reponse);
		return 1;
	}
}
