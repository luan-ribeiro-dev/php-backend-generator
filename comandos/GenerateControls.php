<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateControls extends Command
{
	protected function configure()
	{
		$this->setName('generate:controls')
			->setDescription('Gera todas as classes de controle dos objetos que estão em app/generator/modelos')
			->addArgument('override', InputArgument::OPTIONAL, "Sobreescreve os arquivos do controle em app/controle", "n");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$reponse = false;
		if ($input->getArgument('override') === "s" || $input->getArgument('override') === "1")
			$reponse = true;
		Comandos::generateControls($reponse);
		return 1;
	}
}
