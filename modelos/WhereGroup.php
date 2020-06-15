<?php

namespace DAO;

class WhereGroup extends QueryBuilder
{
  private $type;

  function __construct(string $type = "and")
  {
    $this->type = $type;
  }

  public function save(){}
  public static function all(){}
  public function getArgs(){}
  public static function select(){}
  public function del(){}
  public function get(bool $json = false, bool $single = false, int $limit = null, int $page = 1, bool $appendChilds = true){}


  /**
   * Get the value of type
   * @return string
   */ 
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set the value of type
   *
   * @return self
   */ 
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }
}
