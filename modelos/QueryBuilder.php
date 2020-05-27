<?php
abstract class QueryBuilder
{
	private $table;
	private $columns;
	private $selectColumns = [];
	private $wheres = [];
	private $args = [];
	private $joins = [];
	private $orders = [];
	private $groups = [];
	private $limit = null;

	/**
	 * @var int $last_id
	 */
	private $last_id;

	function __construct(string $table, array $columns)
	{
		$this->table = $table;
		$this->columns = $columns;
	}

	protected function findObject($id)
	{
		$object = null;
		$query = "select * from " . $this->table . " where id = ?";
		$conexao = new ConectaBanco();

		$result = $conexao->executeQuery($query, [$id])[0];

		$conexao->desconectar();

		return $result;
	}

	/**
	 * @return QueryBuilder
	 */
	public function selectColumns(array $selectColumns)
	{
		$this->selectColumns = $selectColumns;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getColumnsString()
	{
		$columnString = "";
		foreach ($this->selectColumns as $column) {
			$columnString .= $column . ", ";
		}
		if ($columnString != "") {
			$columnString = substr($columnString, 0, strlen($columnString) - 2);
		} else {
			$columnString = $this->table . ".* ";
		}
		return $columnString;
	}

	/**
	 * @return QueryBuilder
	 */
	public function where($whereCondition, $arg = null, $type = 'and')
	{
		$this->wheres[] = [
			"condition" => $whereCondition,
			"type" => $type
		];
		$this->args[] = $arg;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getWheresString()
	{
		$whereString = "";
		foreach ($this->wheres as $where) {
			if ($whereString == "") {
				$whereString = "where " . $where['condition'];
			} else {
				if ($where['type'] === 'and') {
					$whereString .= " and ";
				} else if ($where['type'] === 'or') {
					$whereString .= " or ";
				}
				$whereString .= $where['condition'];
			}
		}
		return $whereString;
	}

	/**
	 * @return QueryBuilder
	 */
	private function join(string $joinTable, string $joinCondition, string $innerType = "inner")
	{
		$this->joins[] = [
			'innerType' => $innerType,
			'joinTable' => $joinTable,
			'joinCondition' => $joinCondition,
		];
		return $this;
	}

	/**
	 * @return QueryBuilder
	 */
	public function innerJoin(string $joinTable, string $joinCondition)
	{
		$this->join($joinTable, $joinCondition);
		return $this;
	}

	/**
	 * @return QueryBuilder
	 */
	public function leftJoin(string $joinTable, string $joinCondition)
	{
		$this->join($joinTable, $joinCondition, "left");
		return $this;
	}

	/**
	 * @return QueryBuilder
	 */
	public function rightJoin(string $joinTable, string $joinCondition)
	{
		$this->join($joinTable, $joinCondition, "right");
		return $this;
	}

	/**
	 * @return string
	 */
	private function getJoinsString()
	{
		$joinString = "";
		foreach ($this->joins as $join) {
			$joinString .= $join['innerType'] . " join " . $join['joinTable'] . " on " . $join['joinCondition'] . " ";
		}
		$joinString = substr($joinString, 0, strlen($joinString) - 1);
		return $joinString;
	}

	/**
	 * @return QueryBuilder
	 */
	public function orderBy(string $orderColumn, string $sortType = "asc")
	{
		$this->orders[] = [
			"orderColumn" => $orderColumn,
			"sortType" => $sortType
		];
		return $this;
	}

	/**
	 * @return string
	 */
	private function getOrdersString()
	{
		$orderString = "";
		foreach ($this->orders as $order) {
			if ($orderString == "") {
				$orderString = "order by " . $order['orderColumn'] . " " . $order['sortType'] . ", ";
			} else {
				$orderString .= $order['orderColumn'] . " " . $order['sortType'] . ", ";
			}
		}
		if ($orderString != "") {
			$orderString = substr($orderString, 0, strlen($orderString) - 2);
		}
		return $orderString;
	}

	/**
	 * @return QueryBuilder
	 */
	public function groupBy(string $group)
	{
		$this->groups[] = $group;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getGroupsString()
	{
		$groupsString = "";
		foreach ($this->groups as $group) {
			if ($groupsString == "") {
				$groupsString = "group by " . $group . ", ";
			} else {
				$groupsString .= $group . ", ";
			}
		}
		if ($groupsString != "") {
			$groupsString = substr($groupsString, 0, strlen($groupsString));
		}
		return $groupsString;
	}

	/**
	 * @return QueryBuilder
	 */
	public function limit(int $limit = null)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getLimitString(int $page = null)
	{
		if ($page != null && $this->limit != null) {
			$limit = "limit ";
			$limit .= (($this->limit * $page) - $this->limit) . ", " . ($this->limit);
			return $limit;
		} else {
			return "";
		}
	}

	/**
	 * @return array
	 */
	public function getObjects(int $page = null)
	{
		$query = "select " . $this->getColumnsString()
			. " from " . $this->table . " "
			. $this->getJoinsString() . " "
			. $this->getWheresString() . " "
			. $this->getOrdersString() . " "
			. $this->getGroupsString() . " "
			. $this->getLimitString($page) . " ";
		// echo var_dump([$query, $this->args]);
		try {
			$conexao = new ConectaBanco();
			$result = $conexao->executeQuery($query, $this->args);
			return $result;
		} catch (\Throwable $th) {
			throw $th;
		} finally {
			$conexao->desconectar();
		}
	}

	public function count()
	{
		$query = "select count(" . $this->table . ".id) as quantity from " . $this->table . " "
			. $this->getJoinsString() . " "
			. $this->getWheresString() . " "
			. $this->getOrdersString() . " "
			. $this->getGroupsString();
		try {
			$conexao = new ConectaBanco();
			$result = $conexao->executeQuery($query, $this->args)[0];
			return $result['quantity'];
		} catch (\Throwable $th) {
			throw $th;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * @return bool
	 */
	public abstract function save();

	/**
	 * @var string
	 */
	private function getInsertString()
	{
		$query = "insert into " . $this->table . "(";
		$s = "(";
		foreach ($this->columns as $column) {
			$query .= $column . ", ";
			$s .= "?, ";
		}
		$query = substr($query, 0, strlen($query) - 2) . ")";
		$s = substr($s, 0, strlen($s) - 2) . ")";
		$query .= " values" . $s;
		return $query;
	}

	/**
	 * @return bool
	 */
	public function create()
	{
		try {
			$conexao = new ConectaBanco();
			// echo var_dump($this->getInsertString());
			// echo var_dump($this->getArgs());
			$result = $conexao->executeQuery($this->getInsertString(), $this->getArgs());
			// echo var_dump($result);
			if ($result) {
				$this->setLastId($conexao->getLastInsertedID());
			}
			return $result;
		} catch (Throwable $error) {
			// echo var_dump($error);
			$conexao->desconectar();
			throw $error;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * @var string
	 */
	private function getUpdateString()
	{
		$query = "update " . $this->table . " set ";
		foreach ($this->columns as $column) {
			$query .= $column . " = ?, ";
		}
		$query = substr($query, 0, strlen($query) - 2);
		$query .= " where id = ?";

		return $query;
	}

	/**
	 * @return bool
	 */
	public function update()
	{
		try {
			$conexao = new ConectaBanco();
			$args = $this->getArgs();
			$args[] = $args[0];
			$result = $conexao->executeQuery($this->getUpdateString(), $args);
			return $result;
		} catch (Throwable $error) {
			return $error;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * @var string
	 */
	private function getDeleteString()
	{
		$query = "delete from " . $this->table . " where id = ?";
		return $query;
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		try {
			$conexao = new ConectaBanco();
			$result = $conexao->executeQuery($this->getDeleteString(), [$this->getArgs()[0]]);
			return $result;
		} catch (Throwable $error) {
			return $error;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * @return bool
	 */
	public function deleteWithProcedure($procedureName)
	{
		try {
			$conexao = new ConectaBanco();
			$result = $conexao->executeQuery("call " . $procedureName . "(?)", [$this->getArgs()[0]]);
			return $result;
		} catch (Throwable $error) {
			return $error;
		} finally {
			$conexao->desconectar();
		}
	}

	/**
	 * Get $last_id
	 *
	 * @return int
	 */
	public function getLastId()
	{
		return $this->last_id;
	}

	/**
	 * Set $last_id
	 *
	 * @param int $last_id  $last_id
	 *
	 * @return self
	 */
	public function setLastId(int $last_id)
	{
		$this->last_id = $last_id;

		return $this;
	}

	/**
	 * @return array
	 */
	public abstract static function all();

	/**
	 * @return array
	 */
	public abstract function getArgs();

	/**
	 * @return self
	 */
	public abstract static function select();

	/**
	 * @return bool
	 */
	public abstract function del();
}
