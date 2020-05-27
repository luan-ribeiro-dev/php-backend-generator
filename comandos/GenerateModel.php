<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GenerateModel extends Command
{
	protected function configure()
	{
		$this->setName('generate:model')
			->setDescription('Gera um modelo de um objeto específico que está em app/generator/modelos')
			->addArgument('nome_objeto', InputArgument::REQUIRED, "Nome do objeto que está em app/generator/modelos")
			->addArgument('override', InputArgument::OPTIONAL, "Sobreescreve os arquivos do controle em app/modelo", "n");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$reponse = false;
		if ($input->getArgument('override') === "s" || $input->getArgument('override') === "1")
			$reponse = true;

		Comandos::generateModel($input->getArgument('nome_objeto'), $reponse);
		return 1;
	}
}
