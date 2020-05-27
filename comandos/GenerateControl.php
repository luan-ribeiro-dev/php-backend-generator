<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateControl extends Command
{
	protected function configure()
	{
		$this->setName('generate:control')
			->setDescription('Gera um controle de um objeto específico que está em app/generator/modelos')
			->addArgument('nome_objeto', InputArgument::REQUIRED, "Nome do objeto que está em app/generator/modelos")
			->addArgument('override', InputArgument::OPTIONAL, "Sobreescreve um arquivo do controle em app/controle", 0);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$reponse = false;
		if ($input->getArgument('override') === "s" || $input->getArgument('override') === "1")
			$reponse = true;

		Comandos::generateControl($input->getArgument('nome_objeto'), $reponse);
		return 1;
	}
}
