<?php

class ConectaBanco
{
	/**
	 * Nome do banco de dados
	 * @var string
	 */
	private $nomeBanco = "";
	/**
	 * Caminho do banco de dados
	 * @var string
	 */
	private $caminho = "";
	/**
	 * login do banco de dados
	 * @var string
	 */
	private $login = "";
	/**
	 * Senha do banco de dados
	 * @var string
	 */
	private $senha = "";
	/**
	 * Nome do banco de dados
	 * @var string
	 */
	private $dsn;
	/**
	 * Objeto de conexão
	 * @var PDO
	 */
	private $pdo;
	/**
	 * Variável de declaração
	 * @var PDOStatement
	 */
	private $stmt;

	public function __construct()
	{
		$this->conectar();
	}

	/**
	 * Realiza uma conexão de banco de dados
	 *
	 * @return bool
	 * @throws Exception se a conexão gerar alguma exceção
	 */
	public function conectar(): bool
	{
		$this->dsn = "mysql:host=$this->caminho;dbname=$this->nomeBanco;charset=utf8mb4";
		$options = [
			PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
		];
		$this->pdo = new PDO($this->dsn, $this->login, $this->senha, $options);
		if (!$this->pdo) return false;
		else return true;
	}

	/**
	 * Destrói a conexão com o banco de dados
	 *
	 * @return bool
	 * @throws Exception se a desconexão gerar alguma exceção
	 */
	public function desconectar(): bool
	{
		$this->stmt = null;
		$this->pdo = null;
		return true;
	}

	/**
	 * Executa uma consulta no banco de dados
	 *
	 * @param string $query
	 * @param array $args
	 * @throws Throwable se houver alguma exceção ao executar a consulta
	 */
	public function executeQuery(string $query, $args = null)
	{
		if($this->pdo == null){
			throw new Exception("A conexão com o banco de dados não foi iniciada");
		}
		$i = 0;
		if ($args !== null && count($args) > 0) {
			foreach ($args as $arg) {
				if ($arg === "")
					$args[$i] = null;
				$i++;
			}
		}
		$this->stmt = $this->pdo->prepare($query);
		try {
			// echo $query;
			// echo var_dump($args);
			$r = $this->stmt->execute($args);
			try {
				$result = $this->stmt->fetchAll();
			} catch (PDOException $ex) {
				if($ex->getCode() == 'HY000'){
					$result = $r;
				}else{
					throw $ex;
				}
			}
			if ($result) {
				return $result;
			} else {
				return $this->stmt->rowCount();
			}
		} catch (Throwable $error) {
			throw $error;
		}
	}

	/**
	 * Executa uma procedure pelo nome
	 *
	 * @param string $query
	 * @param array $args
	 * @throws Throwable se houver alguma exceção ao executar a consulta
	 */
	public static function executeProcedure(string $procedureName, $args = null)
	{
		$conexao = new ConectaBanco();
		$i = 0;
		if ($args !== null && count($args) > 0) {
			foreach ($args as $arg) {
				if ($arg === "")
					$args[$i] = null;
				$i++;
			}
		}
		$conexao->stmt = $conexao->pdo->prepare('call ' . $procedureName);
		try {
			// echo $query;
			// echo var_dump($args);
			$conexao->stmt->execute($args);
			$r = $conexao->stmt->execute($args);
			try {
				$result = $conexao->stmt->fetchAll();
			} catch (PDOException $ex) {
				if($ex->getCode() == 'HY000'){
					$result = $r;
				}else{
					throw $ex;
				}
			}
			if ($result) {
				return $result;
			} else {
				return $conexao->stmt->rowCount();
			}
		} catch (Throwable $error) {
			$conexao->desconectar();
			throw $error;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * retorna o último id inserido
	 * @return integer Último ID inserido 
	 */
	public function getLastInsertedID(): int
	{
		return (int) $this->pdo->lastInsertId();
	}

	public function getPDO(): PDO
	{
		return $this->pdo;
	}
}
