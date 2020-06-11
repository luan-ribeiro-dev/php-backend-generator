<?php

namespace Modelo;

use Exception;

class ValidationException extends Exception
{
  /**
   * @var array
   */
  private $errors = [];

  public function __construct(array $errors)
  {
    $this->errors = $errors;
  }

  /**
   * @return array
   */
  public function getErrors()
  {
    $this->message = json_encode($this->errors);
    return json_decode($this->getMessage(), true);
  }

  /**
   * @return array
   */
  public function getErrorAtribute()
  {

    return $this->errors;
  }

  /**
   * Set the value of errors
   *
   * @param array $errors
   *
   * @return self
   */
  public function setErrors(array $errors)
  {
    $this->errors = $errors;

    return $this;
  }

  /**
   * Set the value of errors
   *
   * @param array $errors
   *
   * @return self
   */
  public function addErrors(array $errors)
  {
    $this->errors = array_merge($this->errors, $errors);
    return $this;
  }
}
